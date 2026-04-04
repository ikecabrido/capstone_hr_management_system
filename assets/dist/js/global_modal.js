function openGlobalModal(title, content, isUrl = false) {
  $("#globalModal .modal-title").text(title);
  
  if (isUrl) {
    $("#globalModal .modal-body").html(
      '<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Loading...</div>',
    );
    $("#globalModal").modal("show");
    $("#globalModalBody").load(content);
  } else {
    // Direct HTML content
    $("#globalModal .modal-body").html(content);
    $("#globalModal").modal("show");
  }
}
