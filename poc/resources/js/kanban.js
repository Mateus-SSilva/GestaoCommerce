import Sortable from 'sortablejs';

/**
 * Inicializa uma coluna do Kanban com SortableJS.
 * Ao soltar um cartão, envia ao Livewire: cartão, coluna de destino e nova ordem.
 */
window.kanbanInit = (el, $wire) => {
    if (el._sortable) {
        return;
    }

    el._sortable = Sortable.create(el, {
        group: 'kanban-cards',
        animation: 150,
        draggable: '[data-card-id]',
        ghostClass: 'kanban-ghost',
        dragClass: 'kanban-drag',
        delayOnTouchOnly: true,
        delay: 150,
        onEnd(evt) {
            const cardId = Number(evt.item.dataset.cardId);
            const target = evt.to;
            const columnId = Number(target.dataset.columnId);
            const orderedIds = [...target.querySelectorAll('[data-card-id]')].map((node) => Number(node.dataset.cardId));

            if (evt.from === evt.to && evt.oldIndex === evt.newIndex) {
                return;
            }

            $wire.moveCard(cardId, columnId, orderedIds);
        },
    });
};

window.kanbanReady = true;
window.dispatchEvent(new CustomEvent('kanban:ready'));
