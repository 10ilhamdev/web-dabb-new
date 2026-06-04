function featureDetail() {
    return {
        addSubModal: { open: false, type: 'link', pageType: 'none', isLoginRequired: false },
        editSubModal: { open: false, id: null, name: '', type: 'link', path: '', order: 0, pageType: 'none', newParentId: '', isLoginRequired: false },
        deleteSubModal: { open: false, id: null, name: '' },
        visibilityModal: { open: false, id: null, name: '' },

        openAddSubModal() {
            this.addSubModal = { open: true, type: 'link', pageType: 'none', isLoginRequired: false };
        },
        openEditSubModal(id, name, type, path, order, pageType = 'none', isLoginRequired = false) {
            this.editSubModal = { open: true, id, name, type, path, order, pageType, newParentId: '', isLoginRequired: !!isLoginRequired };
        },
        openDeleteSubModal(id, name) {
            this.deleteSubModal = { open: true, id, name };
        },
        openVisibilityModal(id, name) {
            this.visibilityModal = { open: true, id, name };
        }
    }
}
