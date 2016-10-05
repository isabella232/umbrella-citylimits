<div class="wrap">
<?php $this->_include("header.php") ?>
<h1>
    <?php _e("Import", "wpjobboard") ?> 
</h1>

    <?php $this->_include("flash.php"); ?>

<form action="" method="post" enctype="multipart/form-data">

<script type="text/javascript">
// Custom example logic

var WpjbInterval = null;
var WpjbImportBusy = false;
var WpjbImportIteration = 1;
var WpjbImportTotal = 0;

jQuery(function($) {
    var uploader = new plupload.Uploader({
        runtimes : 'gears,html5,flash,silverlight,browserplus',
        browse_button : 'pickfiles',
        container : 'container',
        max_file_size : '1gb',
        chunk_size : '1mb',
        url : '<?php echo html_entity_decode(wpjb_admin_url("import", "xmlupload", null, array("noheader"=>1))) ?>',
        flash_swf_url : '<?php echo includes_url() ?>/js/plupload/plupload.flash.swf',
        silverlight_xap_url : '<?php echo includes_url() ?>/js/plupload/plupload.silverlight.xap',
        filters : [
            <?php if($canUnzip): ?>{title : "WPJobBoard XML Zip", extensions : "zip"},<?php endif; ?>
            {title : "WPJobBoard XML", extensions : "xml"}
            
        ]
    });

    uploader.bind('Init', function(up, params) {

    });

    $('#uploadfiles').click(function(e) {
        $("#uploadfiles").fadeOut("fast");
        $("#ajax-loading-img").removeClass("ajax-loading");
        $('#pickfiles').css("opacity", "0.5");
        $('#pickfiles').css("visibility", "hidden");

        uploader.start();
        e.preventDefault();
    });

    uploader.init();

    uploader.bind('FilesAdded', function(up, files) {
        $.each(files, function(i, file) {
            $('#filelist').html('<div id="' + file.id + '"><span class="filename">' + file.name + '</span> (' + plupload.formatSize(file.size) + ') <b></b></div>');
            //$("#pickfiles").fadeOut("slow");
        });
        $("#uploadfiles").fadeIn("slow");
        up.refresh(); // Reposition Flash/Silverlight
    });

    uploader.bind('UploadProgress', function(up, file) {
        $('#' + file.id + " b").html(file.percent + "%");
    });

    uploader.bind('Error', function(up, err) {
        $('#filelist').append("<div>Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : "") + "</div>");
        up.refresh(); // Reposition Flash/Silverlight
    });

    uploader.bind('FileUploaded', function(up, file) {
        $('#' + file.id + " b").html("100%");
    });    
	
    uploader.bind('UploadComplete', function(up, files) {

        jQuery("#importlist").html("Starting import. Please wait ...");
        jQuery.ajax({
            url: "<?php echo html_entity_decode(wpjb_admin_url("import", "xmlcount", null, array("noheader"=>1))) ?>",
            success: function(response) {
                WpjbImportTotal += parseInt(response);
                WpjbInterval = setInterval(wpjb_start_import, 100);
            }
        });
        
    });
});

function wpjb_start_import(filename) {

    if(WpjbImportBusy) {
        return;
    }

    WpjbImportBusy = true;
	
    jQuery.ajax({
        type: "POST",
        url: "<?php echo html_entity_decode(wpjb_admin_url("import", "xmlimport", null, array("noheader"=>1))) ?>",
        dataType: "json",
        success: function(response) {

            jQuery("#importlist").html("Importing ("+WpjbImportIteration+"/"+WpjbImportTotal+") "+response.file+"");
            WpjbImportIteration++;
            WpjbImportBusy = false;

            if(response.hasMore == "0") {
                clearInterval(WpjbInterval);
                jQuery("#ajax-loading-img").addClass("ajax-loading");
                jQuery("#importsuccess").show();
            }
        }
    });
}

</script>

<div id="container">
	
    <br />
    <a href="#" id="pickfiles" class="button">
        <span class="wpjb-upload-empty"><?php _e("Select File", "wpjobboard") ?></span>
    </a>

    <div id="filelist" style="margin: 15px 0 15px 0; font-size:12px"></div>
    <div id="importlist" style="margin: 15px 0 15px 0; font-size:12px"></div>
    <div id="importsuccess" style="display:none"><?php _e("Done! You can close this window now.", "wpjobboard") ?></div>

    <input style="display:none" id="uploadfiles" type="submit" value="<?php _e("Upload and start import", "wpjobboard") ?>" class="button-primary" name="Submit"/>
    <img id="ajax-loading-img" class="ajax-loading" src="<?php esc_attr_e(admin_url("/images/wpspin_light.gif")) ?>" />
</div>
			
    
</form>



</div>

<?php $this->_include("footer.php"); ?>

</div>
