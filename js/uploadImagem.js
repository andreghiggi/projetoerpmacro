function showPreview(event){
    if(event.target.files.length > 0){
        var src = URL.createObjectURL(event.target.files[0]);
        var preview = document.getElementById("file-ip-1-preview");
        preview.src = src;
        preview.style.display = "block";
    }
}

$('#btn-remove-imagem').click(() => {
    $('#file-ip-1').val('')
    $('#file-ip-1-preview').attr('src', '/imgs/no-image.png')
})

function showPreview2(event){
    if(event.target.files.length > 0){
        var src = URL.createObjectURL(event.target.files[0]);
        var preview = document.getElementById("file-ip-2-preview");
        preview.src = src;
        preview.style.display = "block";
    }
}

$('#btn-remove-imagem2').click(() => {
    $('#file-ip-2').val('')
    $('#file-ip-2-preview').attr('src', '/imgs/no-image.png')
})
