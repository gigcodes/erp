<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {

        // Datatable 
        const $datatableData = $('#seoTable').DataTable({
            serverSide:true,
            processing:true,
            lengthMenu: [ [50, 100, 150, -1], [50, 100, 150, "All"] ],
            ajax:{
                url:'',
                data:{
                    companyTypeId:() => $(document).find(".typeSelect option:selected").val(),
                    websiteId:() => $(document).find(".websiteFilter option:selected").val(),
                    userId:() => $(document).find(".userFilter option:selected").val(),
                }
            },
            columns:[
                { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                { data: 'company', name:'company' },
                {data:'website.website', name:'website.website'},
                {data:'da', name:'da'},
                {data:'pa', name:'pa'},
                {data:'ss', name:'ss'},
                {data:'user.name', name:'user.name'},
                {data:'userPass', name:'userPass'},
                {data:'liveLink', name:'liveLink'},
                {data:'created_at', name:'created_at'},
                {data:'actions', name:'actions'},
            ]
        });

        // Histroy Table
        $(function() {
            let $historyTable = null;
            $(document).on('click', '.historyBtn', function() {
                let companyId = $(this).attr('data-id');
                let $historyModal = $(document).find('#historyModal');
                $($historyModal).modal('show')
                
                $historyTable = $($historyModal).find('#historyTable').DataTable({
                    serverSide:true,
                    lengthMenu: [ [50, 100, 150, -1], [50, 100, 150, "All"] ],
                    ajax:{
                        url:'',
                        data:{
                            companyId:companyId,
                            type:"COMPANY_HISTORY",
                        },
                    },
                    columns:[
                        { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                        { data: 'company', name:'company' },
                        {data:'website.website', name:'website.website'},
                        {data:'da', name:'da'},
                        {data:'pa', name:'pa'},
                        {data:'ss', name:'ss'},
                        {data:'user.name', name:'user.name'},
                        {data:'userPass', name:'userPass'},
                        {data:'liveLink', name:'liveLink'},
                        {data:'created_at', name:'created_at'},
                    ]
                })
            })
    
            $(document).on('hide.bs.modal', '#historyModal', function() {
                $historyTable.destroy();
                $historyTable = null;
            })
        })

        // Form popup 
        $(function() {
            function initSelect2() {
                $(document).find("select[name=type]").select2({
                    tags: true,
                })
            }

            $(document).on("select2:select", 'select[name=type]',function(e) {
                let data = e.params.data;
                $.ajax({
                    type: "POST",
                    url: `{{ route('seo.content-type.store')}}`,
                    data: {
                        name: data.text,
                        _token:`{{ csrf_token() }}`
                    },
                    dataType: "json",
                    success: function (response) {
                        $(document).find("input[name=type_id]").val(response.data.id)
                    }
                });
            });

            let $formModal = $(document).find('#companyFormModal');
            $(document).on('click', '.editBtn,.addNewBtn', function() {
                let url = $(this).attr('data-url');
                $.ajax({
                    type: "GET",
                    url: url,
                    data: {

                    },
                    dataType: "json",
                    success: function (response) {
                        $($formModal).find('.modal-body').html(response.data);
                        $($formModal).find('.modal-title').text(response.title);
                        $($formModal).modal('show');
                        initSelect2();
                    }
                });
            })

            $(document).on('click', '.saveFormBtn', function() {
                let $form = $(document).find('#companyForm');
                if(!$form.valid()) {
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    dataType: "json",
                    success: function (response) {
                        $($formModal).modal('hide');
                        
                        $datatableData.clear().draw();
                    }
                });
            });

            $($formModal).find('hide.bs.modal', function() {
                $($formModal).find('.modal-body').html('');
            });
        })

        $(document).on('click', '.searchBtn', function() {
            $datatableData.clear().draw();
        })

    });

</script>