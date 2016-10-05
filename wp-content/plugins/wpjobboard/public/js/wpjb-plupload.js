if (typeof wpjb_uploader === 'undefined') {
    wpjb_uploader = [];
}

function wpjb_pluploader_add_file(file) {

    if(file.url == undefined) {
        file.url = "#";
    }
    
    if(file.path == undefined) {
        file.path = "";
    }

    var item = jQuery("<div></div>").addClass("wpjb-upload-item").attr("id", file.id);
    var ft = jQuery("<img />").addClass("wpjb-file-type").attr("alt", "");
    
    var ext = file.name.split(".");
    ext = ext[ext.length-1].toLowerCase();
    ft.attr("src", wpjb_plupload_icons+"/file/file_extension_"+ext+".png");
    
    var div = jQuery("<div></div>").addClass("wpjb-item-actions");
    var i1 = jQuery("<img />").attr("alt", "").attr("title", wpjb_plupload_lang.preview).attr("src", wpjb_plupload_icons+"/ui/bw-photo.png");
    var i2 = jQuery("<img />").attr("alt", "").attr("title", wpjb_plupload_lang.delete_file).attr("src", wpjb_plupload_icons+"/ui/bw-delete.png");
    
    var a1 = jQuery("<a></a>").attr("href", file.url).addClass("wpjb-item-preview").attr("title", wpjb_plupload_lang.preview).attr("target", "blank").append(i1);
    var a2 = jQuery("<a></a>").attr("href", "#"+file.path).addClass("wpjb-item-delete").attr("title", wpjb_plupload_lang.delete_file).append(i2);
    
    a2.click(function() {
       jQuery.ajax({
           url: ajaxurl,
           context: this,
           type: "post",
           dataType: "json",
           data: {
               action: "wpjb_main_delete",
               id: jQuery(this).attr("href").replace("#", "")
           },
           success: function(response) {
               if(response.result == 1) {
                   jQuery(this).closest("div.wpjb-upload-item").fadeOut(function() {
                       var $this = jQuery(this);
                       
                       var id = $this.closest(".wpjb-upload-list").attr("id");
                       $this.remove();
                       wpjb_plupload_handle_limit(jQuery("#wpjb-upload-"+id));
                   });
               } else {
                   alert(response.msg);
               }
           }
       });
       
       return false;
    });
    
    div.append(a1).append(a2);
    
    var b = jQuery("<b></b>").addClass("wpjb-file-name").text(file.name);
    var span = jQuery("<span></span>").addClass("wpjb-file-info").text(plupload.formatSize(file.size));
    
    item.append(ft).append(div).append(b).append(span);
    
    return item;
}

function wpjb_plupload_file_error(fade, msg) {

    fade.empty();
    fade.attr("class", "updated wpjb-upload-error");
    fade.text("Error: "+msg);
    fade.css("cursor", "pointer");
    fade.attr("title", wpjb_plupload_lang.dispose_message);
    fade.click(function() {
        jQuery(this).fadeOut("fast", function() {
            jQuery(this).remove();
        }); 
    });
}

function wpjb_plupload(options) {

    var uploader = new plupload.Uploader(options);

    uploader.init();

    uploader.bind('FilesAdded', function(up, files) {
        var container = up.settings.container;
        jQuery.each(files, function(i, file) {
            jQuery("#"+container).append(wpjb_pluploader_add_file(file));
        });

        up.start();
        up.refresh(); // Reposition Flash/Silverlight
    });

    uploader.bind('UploadProgress', function(up, file) {
        jQuery('#' + file.id + " span.wpjb-file-info").html(file.percent + "%");
    });

    uploader.bind('Error', function(up, err) {
        var div = jQuery("<div></div>");
        jQuery("#"+up.settings.container).append(div);
        wpjb_plupload_file_error(div, err.message)
        up.refresh(); // Reposition Flash/Silverlight
    });

    uploader.bind('FileUploaded', function(up, file, response) {
        var result = jQuery.parseJSON(response.response);
        if(result.result < 1) {
            wpjb_plupload_file_error(jQuery("#"+file.id), result.msg);
            return;
        }
        
        var self = jQuery("#"+file.id);
        
        self.find("span.wpjb-file-info").html(plupload.formatSize(file.size));
        self.find(".wpjb-file-name").html(result.filename);
        self.find(".wpjb-item-preview").attr("href", result.url);
        self.find(".wpjb-item-delete").attr("href", "#"+result.path);
        
        wpjb_plupload_handle_limit(jQuery("#wpjb-upload-"+up.settings.container));

    });
    
    wpjb_uploader.push(uploader);
}

function wpjb_plupload_handle_limit(button) {
    var limit = parseInt(button.attr("href").replace("#", ""));
    var id = button.attr("id").replace("wpjb-upload-", "");
    var uploaded = jQuery("#"+id+" .wpjb-upload-item").length;
    var msg = jQuery("#wpjb-upload-limit-"+id);

    if(uploaded<limit) {
        
        var more_left = wpjb_plupload_lang.x_more_left;
        
        msg.find(".limit-reached").hide();
        msg.find(".limit").show().text(more_left.replace("%d", limit-uploaded));
        button.show();
        wpjb_plupload_refresh()
    } else {
        msg.find(".limit-reached").show();
        msg.find(".limit").hide();
        button.hide();
    }
}

function wpjb_plupload_refresh() {
    
    if (!navigator.userAgent.match(/msie/i) ){
        return;
    }
    
    jQuery.each(wpjb_uploader, function(index, uploader) {
        wpjb_uploader[index].refresh();
    });
}