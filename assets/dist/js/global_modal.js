function openGlobalModal(title, url, onClose) {
  $("#globalModal .modal-title").text(title);
  $("#globalModal .modal-body").html(
    '<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Loading...</div>',
  );
  window._globalModalOnClose = typeof onClose === 'function' ? onClose : null;
  $("#globalModal").modal("show");
  $("#globalModalBody").load(url);
}

$("#globalModal").on('hidden.bs.modal', function () {
  if (typeof window._globalModalOnClose === 'function') {
    try {
      window._globalModalOnClose();
    } catch (e) {
      console.error('Error in modal onClose callback:', e);
    }
  }
  window._globalModalOnClose = null;
});
