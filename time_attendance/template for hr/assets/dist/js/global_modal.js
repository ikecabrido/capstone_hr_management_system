function openGlobalModal(title, url) {
  $("#globalModal .modal-title").text(title);
  $("#globalModal .modal-body").html(
    '<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Loading...</div>',
  );
  $("#globalModal").modal("show");
  $("#globalModalBody").load(url);
}
