$(document).ready(function () {
    var process = false;

    $(document).on('click', '.block-upload-img .thumbnail-img img', function () {
            $(this).next('input').trigger('click');
    });
    
    $(document).on('click', '.cancel-gallery', function( event ) {
        $(this).closest('.block-upload-img').remove();
    });

    $(document).on('change', '.block-upload-img .thumbnail-img input', function () {
        if (!process) {
            process = true;

            var file_data = $(this).prop('files')[0];
            var type_upload = $('#type_upload').val();
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('type_upload', type_upload);

            $this = $(this);
            $this.prev('img').attr('src', '');
            $this.closest('.thumbnail-img').addClass('loadding');

            $.ajax({
                'url': $('#load_upload_image_ajax').val(),
                'type': 'POST',
                'dataType': 'json',
                'cache': false,
                'contentType': false,
                'processData': false,
                'data': form_data,
                'success': function (response) {

                    if (typeof response != 'undefined') {
                        if (response.status != 200) {
                            alert(response.message);
                            return false;
                        }

                        $this.closest('.thumbnail-img').removeClass('loadding');
                        $this.closest('.thumbnail-img').find('.fa').fadeIn();
                        $this.next('input').val(response.result.image);
                        $this.prev('img').attr('src', response.result.image_url);
                    } else {
                        alert('An unknown error occurred, please try again.');
                    }
                }
            }).done(function () {
                process = false;
            });
        }
    });

    $('.block-upload-img').on('click', '.delete-img', function () {
        if (!process) {
            process = true;

            var image = $(this).closest('.thumbnail-img').find('.input_image').val();
            var folder = $('#folder_upload').val();
            $this = $(this);

            $.ajax({
                'url': $('#load_delete_image_ajax_url').val(),
                'type': 'GET',
                'data': {'image': image, 'folder': folder},
                'success': function (response) {
                    if (typeof response != 'undefined') {
                        if (response.status != 200) {
                            alert(response.message);
                            return false;
                        }

                        $this.closest('.thumbnail-img').find('.fa').fadeOut();
                        $this.closest('.thumbnail-img').find('input[type="file"]').val('');
                        $this.closest('.thumbnail-img').find('.input_image').val('');
                        $this.closest('.thumbnail-img').find('img').attr('src', $('#asset_choose_image_url').val());
                    } else {
                        alert('An unknown error occurred, please try again.');
                    }
                }
            }).done(function () {
                process = false;
            });
        }
    });
});