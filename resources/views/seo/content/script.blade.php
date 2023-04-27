<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        let kwRowIdCount = 1;
        $(document).on('click', '.addKeywordBtn', function() {
            let $kwRow = $('.kwRow:last').clone();
            $('input', $kwRow).val('');
            $('select option', $kwRow).removeAttr('selected');
            $kwRow.appendTo($('.kwRowSec'));
            kwRowIdCount++;
        });

        $(document).on('click', '.kwRow .kwRmBtn', function() {
            let $kwSec = $(document).find('.kwRowSec');
            if ($kwSec.find('.kwRow').length != 1) {
                let $per = $(this).closest('.kwRow');
                $per.remove();
            }
        });

        $(function() {
            let $formModal = $(document).find('#formModal');
            $(document).on('click', '.addNewBtn', function() {
                $($formModal).modal('show');
                $.ajax({
                    type: "GET"
                    , url: "{{ route('seo.content.create') }}"
                    , data: {
                        formType: "CREATE_FORM"
                    }
                    , dataType: "json"
                    , success: function(response) {
                        $($formModal).find('.modal-body').html(response.data)
                    }
                });
            });

            $($formModal).on('hide.bs.modal', function() {
                $($formModal).find('.modal-body').html('');
            })
            $(document).on('click', '.saveFormBtn', function(e) {
                let $form = $(document).find('#seoForm');
                if(!$form.valid()) {
                    return false;
                }
                $.ajax({
                    type: "POST"
                    , url: $form.attr('action')
                    , data: $form.serialize()
                    , dataType: "json"
                    , success: function(response) {
                        if(response.success) {
                            $($formModal).modal('hide');
                            $('#seoProcessTbl').DataTable().clear().draw();
                        }
                    }
                });
            })
        })
    });

</script>
