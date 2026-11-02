<?php

namespace App\Services;

use App\Models\Board;
use App\Models\Card;
use App\Models\Comment;
use App\Models\MonitorIssue;
use App\Models\MonitorRun;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Agente de monitoramento — VERSÃO SIMULADA para a POC.
 *
 * Na versão real (Fase 2), o método detect() consulta as APIs oficiais de cada canal
 * (Mercado Livre, Shopee, Magazord…) e compara com a referência de catálogo.
 * Aqui ele sorteia problemas de uma lista fictícia para demonstrar o fluxo:
 * detecção → deduplicação → tarefa para o responsável → fechamento automático.
 */
class MonitorAgent
{
    public const BOARD_NAME = 'Marketplaces';

    /** Regra de roteamento: canal → e-mail do responsável. */
    public const ROUTING = [
        'Mercado Livre' => 'bruno@demo.test',
        'Shopee' => 'carla@demo.test',
        'SHEIN' => 'carla@demo.test',
        'Magazord' => 'diego@demo.test',
    ];

    public const SEVERITY_TO_PRIORITY = [
        'critico' => 'urgente',
        'alto' => 'alta',
        'medio' => 'media',
        'info' => 'baixa',
    ];

    public function run(): MonitorRun
    {
        $detected = $this->detect();
        $stats = ['new' => 0, 'updated' => 0, 'resolved' => 0, 'cards' => 0];

        DB::transaction(function () use ($detected, &$stats) {
            $seen = [];

            foreach ($detected as $data) {
                $fingerprint = md5($data['channel'].'|'.$data['listing_ref'].'|'.$data['type']);
                $seen[] = $fingerprint;

                $issue = MonitorIssue::where('fingerprint', $fingerprint)->first();

                if ($issue && $issue->status === 'aberto') {
                    // Deduplicação: o problema continua → atualiza, não cria outra tarefa.
                    $issue->update(['last_seen_at' => now(), 'occurrences' => $issue->occurrences + 1]);
                    $stats['updated']++;

                    continue;
                }

                $issue ??= new MonitorIssue(['fingerprint' => $fingerprint, 'first_seen_at' => now()]);
                $issue->fill(Arr::except($data, []) + [
                    'status' => 'aberto',
                    'last_seen_at' => now(),
                    'resolved_at' => null,
                    'occurrences' => 1,
                ]);
                $issue->card_id = $this->createCard($issue)?->id;
                $issue->save();

                $stats['new']++;
                $stats['cards'] += $issue->card_id ? 1 : 0;
            }

            // Fechamento automático: problemas abertos que não apareceram nesta verificação.
            MonitorIssue::where('status', 'aberto')->whereNotIn('fingerprint', $seen)->get()
                ->each(function (MonitorIssue $issue) use (&$stats) {
                    $issue->update(['status' => 'resolvido', 'resolved_at' => now()]);
                    $this->moveCardToValidation($issue);
                    $stats['resolved']++;
                });
        });

        return MonitorRun::create([
            'checked_listings' => count(self::catalog()) * 4,
            'new_issues' => $stats['new'],
            'updated_issues' => $stats['updated'],
            'resolved_issues' => $stats['resolved'],
            'cards_created' => $stats['cards'],
        ]);
    }

    protected function createCard(MonitorIssue $issue): ?Card
    {
        $board = Board::where('name', self::BOARD_NAME)->first();
        $column = $board?->columns()->where('name', 'A fazer')->first()
            ?? $board?->columns()->where('is_done', false)->orderBy('position')->first();
        if (! $column) {
            return null;
        }

        $assignee = User::where('email', self::ROUTING[$issue->channel] ?? null)->first();
        $daysToFix = match ($issue->severity) { 'critico' => 0, 'alto' => 1, default => 3 };

        Card::where('board_column_id', $column->id)->increment('position');

        return Card::create([
            'board_id' => $board->id,
            'board_column_id' => $column->id,
            'title' => "[{$issue->channel}] {$issue->message}",
            'description' => "Produto: {$issue->product_name}\nSKU: {$issue->sku}\nAnúncio: {$issue->listing_ref}\n\nSugestão do agente: {$issue->suggestion}\n\n(Tarefa criada automaticamente pelo agente de monitoramento.)",
            'priority' => self::SEVERITY_TO_PRIORITY[$issue->severity] ?? 'media',
            'due_date' => today()->addWeekdays($daysToFix),
            'assignee_id' => $assignee?->id,
            'origin' => 'agente',
            'channel' => $issue->channel,
            'position' => 0,
        ]);
    }

    protected function moveCardToValidation(MonitorIssue $issue): void
    {
        $card = $issue->card;
        if (! $card || $card->completed_at) {
            return;
        }

        $validate = $card->board->columns()->where('name', 'Validar')->first();
        if ($validate && $card->board_column_id !== $validate->id) {
            Card::where('board_column_id', $validate->id)->increment('position');
            $card->update(['board_column_id' => $validate->id, 'position' => 0]);
        }

        Comment::create([
            'card_id' => $card->id,
            'user_id' => null,
            'body' => '🤖 Agente: o problema não foi encontrado na verificação de '.now()->format('d/m H:i').'. Validar e concluir.',
        ]);
    }

    /** SIMULAÇÃO: sorteia quais problemas "existem" nesta verificação. */
    protected function detect(): array
    {
        $pool = self::issuePool();
        $open = MonitorIssue::where('status', 'aberto')->pluck('fingerprint')->all();

        $firstRun = $open === [] && ! MonitorIssue::exists();
        $result = [];
        foreach ($pool as $item) {
            $fp = md5($item['channel'].'|'.$item['listing_ref'].'|'.$item['type']);
            $isOpen = in_array($fp, $open, true);
            // Problemas já abertos persistem com 70% de chance; novos aparecem com 30% (55% na primeira execução).
            if (mt_rand(1, 100) <= ($isOpen ? 70 : ($firstRun ? 55 : 30))) {
                $result[] = $item;
            }
        }

        return $result;
    }

    public static function catalog(): array
    {
        return [
            'PJ-FLA-XAD' => 'Pijama Longo Flanela Xadrez',
            'PJ-MAL-LUA' => 'Pijama Curto Malha Lua e Estrelas',
            'PJ-SED-LIS' => 'Pijama Americano Seda Lisa',
            'PJ-INF-DIN' => 'Pijama Infantil Dinossauros',
            'CAM-ALG-LIS' => 'Camisola Algodão Lisa',
            'PJ-FAM-NAT' => 'Pijama Família Natal',
            'PJ-SOF-URS' => 'Pijama Soft Ursinhos',
            'ROB-PLU-CIN' => 'Robe Plush Cinza',
        ];
    }

    public static function issuePool(): array
    {
        $c = self::catalog();

        return [
            ['channel' => 'Mercado Livre', 'type' => 'pausado', 'severity' => 'critico', 'sku' => 'PJ-FLA-XAD-M-AZ', 'listing_ref' => 'MLB4012345678', 'product_name' => $c['PJ-FLA-XAD'], 'message' => 'Anúncio pausado por falta de estoque com estoque disponível na loja', 'suggestion' => 'Reenviar estoque (referência Magazord: 34 un.).'],
            ['channel' => 'Mercado Livre', 'type' => 'catalogo', 'severity' => 'alto', 'sku' => 'PJ-MAL-LUA-G-RS', 'listing_ref' => 'MLB4023456789', 'product_name' => $c['PJ-MAL-LUA'], 'message' => 'Aviso de catálogo obrigatório: anúncio será moderado em 5 dias', 'suggestion' => 'Associar o anúncio ao produto de catálogo (opt-in).'],
            ['channel' => 'Mercado Livre', 'type' => 'preco', 'severity' => 'alto', 'sku' => 'PJ-SED-LIS-P-PR', 'listing_ref' => 'MLB4034567890', 'product_name' => $c['PJ-SED-LIS'], 'message' => 'Preço R$ 89,90 diferente da regra do canal (R$ 119,90)', 'suggestion' => 'Conferir se houve promoção não autorizada; reaplicar a regra de preço.'],
            ['channel' => 'Mercado Livre', 'type' => 'atributo', 'severity' => 'medio', 'sku' => 'PJ-INF-DIN-4', 'listing_ref' => 'MLB4045678901', 'product_name' => $c['PJ-INF-DIN'], 'message' => 'Atributo obrigatório "Idade recomendada" não preenchido', 'suggestion' => 'Preencher o atributo para evitar perda de exposição.'],
            ['channel' => 'Shopee', 'type' => 'banido', 'severity' => 'critico', 'sku' => 'PJ-FAM-NAT-KIT', 'listing_ref' => 'SHP-22871301', 'product_name' => $c['PJ-FAM-NAT'], 'message' => 'Anúncio BANIDO: imagem com texto/marca d\'água', 'suggestion' => 'Trocar a imagem principal por foto limpa (fundo neutro, sem texto).'],
            ['channel' => 'Shopee', 'type' => 'estoque', 'severity' => 'alto', 'sku' => 'PJ-SOF-URS-M-BG', 'listing_ref' => 'SHP-22871455', 'product_name' => $c['PJ-SOF-URS'], 'message' => 'Estoque divergente: 12 un. no canal × 0 un. na loja', 'suggestion' => 'Zerar estoque no canal para evitar venda sem produto.'],
            ['channel' => 'Shopee', 'type' => 'grade', 'severity' => 'medio', 'sku' => 'PJ-MAL-LUA', 'listing_ref' => 'SHP-22871590', 'product_name' => $c['PJ-MAL-LUA'], 'message' => 'Grade quebrada: tamanhos M e G zerados em produto ativo', 'suggestion' => 'Avaliar reposição (12 pessoas no avise-me da loja).'],
            ['channel' => 'Shopee', 'type' => 'inativo', 'severity' => 'alto', 'sku' => 'CAM-ALG-LIS-G-BR', 'listing_ref' => 'SHP-22871622', 'product_name' => $c['CAM-ALG-LIS'], 'message' => 'Anúncio deslistado (UNLIST) sem registro de ação da equipe', 'suggestion' => 'Verificar quem deslistou e reativar se foi engano.'],
            ['channel' => 'SHEIN', 'type' => 'moderado', 'severity' => 'alto', 'sku' => 'ROB-PLU-CIN-U', 'listing_ref' => 'SHEIN-SKC-88124', 'product_name' => $c['ROB-PLU-CIN'], 'message' => 'Produto reprovado na revisão: tabela de medidas ausente', 'suggestion' => 'Enviar tabela de medidas padrão do robe.'],
            ['channel' => 'SHEIN', 'type' => 'imagem', 'severity' => 'medio', 'sku' => 'PJ-SED-LIS-M-VD', 'listing_ref' => 'SHEIN-SKC-88177', 'product_name' => $c['PJ-SED-LIS'], 'message' => 'Imagem de amostra de cor fora do padrão (menos de 900 px)', 'suggestion' => 'Reenviar imagem quadrada entre 900 e 2200 px.'],
            ['channel' => 'Magazord', 'type' => 'sem_anuncio', 'severity' => 'info', 'sku' => 'PJ-INF-DIN-8', 'listing_ref' => 'Loja própria', 'product_name' => $c['PJ-INF-DIN'], 'message' => 'SKU novo na loja ainda não publicado em nenhum marketplace', 'suggestion' => 'Incluir na fila de cadastro dos canais.'],
            ['channel' => 'Magazord', 'type' => 'faturamento', 'severity' => 'alto', 'sku' => '—', 'listing_ref' => 'Pedido #58231', 'product_name' => 'Pedido do site', 'message' => 'Pedido travado no faturamento há mais de 24 h', 'suggestion' => 'Verificar inscrição estadual / dados fiscais do cliente.'],
        ];
    }
}
