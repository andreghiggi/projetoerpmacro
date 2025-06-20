!(function (e) {
    e.extend({
        uploadPreview: function (l) {

            var i = e.extend(

                {
                    input_field: "#inp-image-input",
                    preview_box: "#inp-image-preview",
                    label_field: "#inp-image-label",
                    label_default: "Selecione a imagem",
                    label_selected: "Selecione a imagem",
                    no_label: !1,
                    success_callback: null,
                },
                

            {
                input_field: ".image-input",
                preview_box: ".image-preview",
                label_field: ".image-label",
                label_default: "Selecione a imagem",
                label_selected: "Selecione a imagem",
                no_label: !1,
                success_callback: null,
            },
            l

            );
            return window.File && window.FileList && window.FileReader
            ? void (
              void 0 !== e(i.input_field) &&
              null !== e(i.input_field) &&
              e(i.input_field).change(function () {
                console.clear()

                $img = $(this).next();
                var l = this.files;
                if (l.length > 0) {
                  var a = l[0],
                  o = new FileReader();
                  o.addEventListener("load", function (l) {
                      var o = l.target;
                      $img.attr('src', o.result);
                      // $img.addClass("d-none");
                      // a.type.match("image")
                      // ? ($img.css(
                      //   "background-image",
                      //   "url(" + o.result + ")"
                      //   ),
                      // e(i.preview_box).css(
                      //   "background-size",
                      //   "cover"
                      //   ),
                      // e(i.preview_box).css(
                      //   "background-position",
                      //   "center center"
                      //   ))
                      // : a.type.match("audio")
                      // ? e(i.preview_box).html(
                      //   "<audio controls><source src='" +
                      //   o.result +
                      //   "' type='" +
                      //   a.type +
                      //   "' />Your browser does not support the audio element.</audio>"
                      //   )
                      // : alert(
                      //   "Este tipo de arquivo ainda não é suportado."
                      //   );
                  }),
                  0 == i.no_label &&
                  e(i.label_field).html(i.label_selected),
                  o.readAsDataURL(a),
                  i.success_callback && i.success_callback();
              } else 0 == i.no_label && e(i.label_field).html(i.label_default), e(i.preview_box).css("background-image", "none"), e(i.preview_box + " audio").remove();
          })
              )
            : (alert(
              "Você precisa de um navegador com suporte a leitor de arquivos para usar este formulário corretamente."
              ),
            !1);
        },
    });
})(jQuery);

$.uploadPreview({
    input_field: "#inp-image-upload",
    preview_box: "#inp-image-preview",
    label_field: "#inp-image-label",
});

$.uploadPreview({
    input_field: "._image-upload",
    preview_box: "._image-preview",
    label_field: "._image-label",
});
