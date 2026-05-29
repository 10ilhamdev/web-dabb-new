function featureDetail() {
    return {
        addSubModal: { open: false, type: 'link', pageType: 'none' },
        editSubModal: { open: false, id: null, name: '', type: 'link', path: '', order: 0, pageType: 'none', newParentId: '' },
        deleteSubModal: { open: false, id: null, name: '' },

        openAddSubModal() {
            this.addSubModal = { open: true, type: 'link', pageType: 'none' };
        },
        openEditSubModal(id, name, type, path, order, pageType = 'none') {
            this.editSubModal = { open: true, id, name, type, path, order, pageType, newParentId: '' };
        },
        openDeleteSubModal(id, name) {
            this.deleteSubModal = { open: true, id, name };
        }
    }
}
