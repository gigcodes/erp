var productTemplate = {
    init: function(settings) {
        
        productTemplate.config = {
            bodyView: settings.bodyView
        };
        
        $.extend(productTemplate.config, settings);
        
        this.getResults();

        //initialize pagination
        productTemplate.config.bodyView.on("click",".page-link",function(e) {
        	e.preventDefault();
        	productTemplate.getResults($(this).attr("href"));
        });

        //create producte template
        productTemplate.config.bodyView.on("click",".create-product-template-btn",function(e) {
            productTemplate.openForm();
        });

        // delete product templates
        productTemplate.config.bodyView.on("click",".btn-delete-template",function(e) {
            if(!confirm("Are you sure you want to delete record?")) {
                return false;
            }else {
                productTemplate.deleteRecord($(this));
            }
        });

        $(document).on("click",".imgAdd",function(e) {
            $(this).closest(".row").find('.imgAdd').before('<div class="col-sm-3 imgUp"><div class="imagePreview"></div><label class="btn btn-primary">Upload<input type="file" class="uploadFile img" value="Upload Photo" style="width:0px;height:0px;overflow:hidden;"></label><i class="fa fa-times del"></i></div>');
        });

        $(document).on("click","i.del",function(e) {
            $(this).parent().remove();
        });



        $(document).on("change",".uploadFile", function() {
            var uploadFile = $(this);
            var files = !!this.files ? this.files : [];
            if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
     
            if (/^image/.test( files[0].type)){ // only image file
                var reader = new FileReader(); // instance of the FileReader
                reader.readAsDataURL(files[0]); // read the local file
     
                reader.onloadend = function(){ // set image data as background of div
                    //alert(uploadFile.closest(".upimage").find('.imagePreview').length);
                    uploadFile.closest(".imgUp").find('.imagePreview').css("background-image", "url("+this.result+")");
                }
            }
        });

        $(document).on("click",".create-product-template",function(e){
            if ($("#product-template-from").valid()) {
                productTemplate.submitForm($(this));
            }
        });
    },
    validationRule : function() {
         $(document).find("#product-template-from").validate({
            rules: {
                template_no     : "required",
                product_title   : "required",
                brand_id        : "required",
                currency        : {
                                    required: true,
                                    minlength: 3,
                                    maxlength: 3
                                }
            },
            messages: {
                template_no     : "Please Select Template No",
                product_title   : "Please Enter Product Title",
                brand_id        : "Please Select Brand",
                currency        : {
                                    required: "Please Enter Currency",
                                    minlength: "Please Enter least {0} characters",
                                    maxlength: "Please Enter {0} characters"
                                }
            }
        })
    },
    getResults: function(href) {
    	var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/product-templates/response",
            method: "get",
        }
        this.sendAjax(_z, "showResults");
    },
    showResults : function(response) {
    
    	var addProductTpl = $.templates("#product-templates-result-block");
        var tplHtml       = addProductTpl.render(response);
    	productTemplate.config.bodyView.find("#page-view-result").html(tplHtml);

    },
    openForm : function() {
        var addProductTpl = $.templates("#product-templates-create-block");
        var tplHtml       = addProductTpl.render({});
        $("#display-area").html(tplHtml);
        $("#product-template-create-modal").modal("show");
        productTemplate.validationRule();
        $(".select-2-brand").select2({
            width:"100%"
        });
        productTemplate.productSearch();

        productTemplate.changeTemplateNo();
        $('.template_no').trigger('change');
        $(document).on("change",".ddl-select-product",function(e){
            var _z = {
                url: "/product-templates/select-product-id",
                data : {
                    'product_id' : $(this).val()
                }
            }
            productTemplate.sendAjax(_z, "selectProductId");
        });

        $( ".show-product-image" ).sortable();
    },
    selectProductId : function(response) {
         $('.show-product-image').html(response.data);
    },
    changeTemplateNo : function(response) {
        var changeTemplateImage = ["\/images\/product-templates\/Bvlgari Bag 17-09.jpg","\/images\/product-templates\/Chloe Bag 17-09.jpg","\/images\/product-templates\/Fendi Bag 20-09.jpg","\/images\/product-templates\/Fendi Shoes 19-09.jpg","\/images\/product-templates\/Fendi T-shirt.jpg","\/images\/product-templates\/Ferragamo Sneaker.jpg","\/images\/product-templates\/Gucci Bag 13-08.jpg","\/images\/product-templates\/Gucci Bag 16-08.jpg","\/images\/product-templates\/Gucci Marmont Bag.jpg","\/images\/product-templates\/Gucci Marmont Sachel.jpg","\/images\/product-templates\/Gucci Marmont Top Handle Bag.jpg","\/images\/product-templates\/Gucci Mens Shoes.jpg","\/images\/product-templates\/Gucci Sneakers.jpg","\/images\/product-templates\/Gucci Women Shoes.jpg","\/images\/product-templates\/Kenzo T-shirt 19-09 A.jpg","\/images\/product-templates\/Marc Jacob 04-09-2019.jpg","\/images\/product-templates\/Off-white02-09 A.jpg","\/images\/product-templates\/Off-white02-09 B.jpg","\/images\/product-templates\/Prada Sneakers.jpg","\/images\/product-templates\/Sliders.jpg","\/images\/product-templates\/Tods Lofers.jpg","\/images\/product-templates\/Tory Miller 10-05 B.jpg","\/images\/product-templates\/Valentino Bag 1.jpg","\/images\/product-templates\/Valentino Bag.jpg","\/images\/product-templates\/YSL Bag 1.jpg","\/images\/product-templates\/YSL Bag.jpg","\/images\/product-templates\/YSL Sneakers.jpg","\/images\/product-templates\/jimmychoo03-09.jpg","\/images\/product-templates\/tory-tote.jpg"]; 
        
        $(document).on("change",".template_no",function(e){
            var id = $(this).val();
            var html = '';
            if (changeTemplateImage && changeTemplateImage[id]) {
                html = '<img src="'+changeTemplateImage[id]+'" width="100%">';
            }
            $('.image_template_no').html(html);
        });
    },
    submitForm : function(ele) {
        var form = ele.closest("#product-template-create-modal").find("form");
        var formData = new FormData(form[0]);
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/product-templates/create",
            method: "post",
            data : formData
        }
        this.sendFormDataAjax(_z, "closeForm");
    },
    closeForm : function(response) {
        if(response.code == 1) {
            location.reload();
        }
    },
    deleteRecord : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/product-templates/destroy/"+ele.data("id"),
            method: "get",
        }
        this.sendAjax(_z, 'getResults', true);
    },
    productSearch : function() {
        $('.ddl-select-product').select2({
            ajax: {
                url: '/productSearch',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search for Product by id, Name, Sku',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 2,
            width: '100%',
            templateResult: function (product) {
                if (product.loading) {
                    return product.sku;
                }

                if (product.sku) {
                    return "<p> <b>Id:</b> " + product.id + (product.name ? " <b>Name:</b> " + product.name : "") + " <b>Sku:</b> " + product.sku + " </p>";
                }

            },
            templateSelection: function (product) {
                
                $(document).find("#product-template-from").find('.product_title').val('');
                if (product.name) {
                    $(document).find("#product-template-from").find('.product_title').val(product.name);
                }

                $(document).find("#product-template-from").find('.select-2-brand').val('').trigger('change');
                if (product.brand) {
                    $(document).find("#product-template-from").find('.select-2-brand').val(product.brand).trigger('change');
                }

                return product.name;
            }
        });
    }
}

$.extend(productTemplate, common);