/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.skin = 'bootstrapck';
    config.removePlugins = 'save,newpage,preview,print';
    config.language = 'ru';
    config.filebrowserImageBrowseUrl = CKEDITOR.basePath+ 'filemanager/dialog.php?type=1&editor=ckeditor&fldr=';
    config.filebrowserUploadUrl  = CKEDITOR.basePath+ 'filemanager/dialog.php?type=2&editor=ckeditor&fldr=';
    config.filebrowserBrowseUrl      = CKEDITOR.basePath+ 'filemanager/dialog.php?type=2&editor=ckeditor&fldr=';
    config.protectedSource.push( /<script[\s\S]*?script>/g );
};
