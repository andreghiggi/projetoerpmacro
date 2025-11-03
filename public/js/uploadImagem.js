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

function image_input_on_change( event ) {
    if(event.target.files.length > 0){
        const src                   = URL.createObjectURL(event.target.files[0]);
        let   target                = $(event.target)
        const current_frame         = target.closest('div[id^=image_frame_]').eq(0)
        const index                 = parseInt(current_frame.attr('id').split("_")[2])
        let   preview               = $(`#image_preview_${index}`, current_frame);
        preview.attr('src', src);
        preview.css('display', 'block');
        $(`#image_list_${index}`, current_frame).val('');

    }
}

let image_index = $("div[id^=image_frame], div[id^=image_variacao_frame]").length;


$(document).on('click', 'button[id^=image_remove]', function(event) {
    const target          = $(event.target);
    const current_frame   = target.closest("[id^=image_frame]")
    const index           = parseInt(current_frame.attr('id').split("_")[2])
    current_frame.remove();
});

$(document).on('click', 'button[id^=image_add]', function(event) {
    const target          = $(event.target);
    const current_frame   = target.closest("[id^=image_frame]")
    const index           = parseInt(current_frame.attr('id').split("_")[2])
    const new_index       = image_index++;
    const image_frame     = $(`#image_frame_${index}`)
    
    let image_frame_new = image_frame.clone(false, false)
    
    image_frame_new.attr('id', `image_frame_${new_index}`)
    
    $(`#image_add_${index}`, image_frame_new).attr('id', `image_add_${new_index}`);
    $(`#image_remove_${index}`, image_frame_new).attr('id', `image_remove_${new_index}`);
    $(`#image_preview_${index}`, image_frame_new).attr('id', `image_preview_${new_index}`);
    $(`#image_input_label_${index}`, image_frame_new).attr('id', `image_input_label_${new_index}`);
    $(`#image_input_label_${new_index}`, image_frame_new).attr('for', `image_input_${new_index}`);
    $(`#image_list_${index}`, image_frame_new).attr('id', `image_list_${new_index}`);
    $(`#image_list_${new_index}`, image_frame_new).val('');
    
    const input = $('input[type="file"]', image_frame_new);
    input.attr('id', `image_input_${new_index}`);
    input.attr('data-index', new_index);
    input.val(''); 
    input.on('change', image_input_on_change); 
    
    image_frame.after(image_frame_new);
});

// variações

function image_variacao_input_on_change( event ) {
    if(event.target.files.length > 0){
        const src            = URL.createObjectURL(event.target.files[0]);
        let   target         = $(event.target)
        const current_frame  = target.closest('div[id^=image_variacao_frame_]').eq(0)
        const id_parts       = current_frame.attr('id').split("_")
        const index          = parseInt(id_parts[4])
        const variacao_index = parseInt(id_parts[3])
        let   preview        = $(`#image_variacao_preview_${variacao_index}_${index}`, current_frame);
        preview.attr('src', src);
        preview.css('display', 'block');
        $(`#image_variacao_list_${variacao_index}_${index}`, current_frame).val('');
    }
}

$(document).on('click', 'button[id^=image_variacao_remove]', function(event) {
    const target          = $(event.target);
    const current_frame   = target.closest("[id^=image_variacao_frame]")
    const index           = parseInt(current_frame.attr('id').split("_")[3])
    current_frame.remove();
});

$(document).on('click', 'button[id^=image_variacao_add]', function(event) {
    const target         = $(event.target);
    const current_frame  = target.closest("[id^=image_variacao_frame]")
    const id_parts       = current_frame.attr('id').split("_")
    const index          = parseInt(id_parts[4])
    const variacao_index = parseInt(id_parts[3])
    const new_index      = image_index++;
    const image_frame    = $(`#image_variacao_frame_${variacao_index}_${index}`)
    
    let image_frame_new = image_frame.clone(false, false)
    
    image_frame_new.attr('id', `image_variacao_frame_${variacao_index}_${new_index}`)
    
    $(`#image_variacao_add_${variacao_index}_${index}`, image_frame_new).attr('id', `image_variacao_add_${variacao_index}_${new_index}`);
    $(`#image_variacao_remove_${variacao_index}_${index}`, image_frame_new).attr('id', `image_variacao_remove_${variacao_index}_${new_index}`);
    $(`#image_variacao_preview_${variacao_index}_${index}`, image_frame_new).attr('id', `image_variacao_preview_${variacao_index}_${new_index}`);
    $(`#image_variacao_input_label_${variacao_index}_${index}`, image_frame_new).attr('id', `image_variacao_input_label_${variacao_index}_${new_index}`);
    $(`#image_variacao_input_label_${variacao_index}_${new_index}`, image_frame_new).attr('for', `image_variacao_input_${variacao_index}_${new_index}`);
    $(`#image_variacao_list_${variacao_index}_${index}`, image_frame_new).attr('id', `image_variacao_list_${variacao_index}_${new_index}`);
    $(`#image_variacao_list_${variacao_index}_${new_index}`, image_frame_new).val('');
    
    const input = $('input[type="file"]', image_frame_new);
    input.attr('id', `image_variacao_input_${variacao_index}_${new_index}`);
    input.val(''); 
    input.on('change', image_variacao_input_on_change); 
    
    image_frame.after(image_frame_new);
});

$('[id^=image_input_]').on('change', image_input_on_change);
$('input[id^=image_variacao_input_]').on('change', image_variacao_input_on_change);
