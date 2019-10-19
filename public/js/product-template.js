var productTemplate = {
    init: function(settings) {
        
        productTemplate.config = {
            bodyView: settings.bodyView
        };
        
        $.extend(productTemplate.config, settings);
        
        console.log("Product Template init :", productTemplate);
        console.log("Start page request : ", [1]);
        
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
    },
    getResults: function(href) {
    	var _z = {
            url: (href) ? href : this.config.baseUrl + "/product-templates/response",
            method: "get",
        }
        this.sendAjax(_z, "showResults");
    },
    showResults : function(response) {
    
    	var addProductTpl = $.templates("#product-templates-result-block");
        var tplHtml       = addProductTpl.render(response);
    	productTemplate.config.bodyView.find("#page-view-result").html(tplHtml);
    	
    	console.log("Result Intialized .. ", []);
    },
    openForm : function() {
        var addProductTpl = $.templates("#product-templates-create-block");
        var tplHtml       = addProductTpl.render({});
        $("#display-area").html(tplHtml);
        $("#product-template-create-modal").modal("show");
    }
}

$.extend(productTemplate, common);