CKEDITOR.config.fontSize_sizes='80%/85%;90%/93%;100%/100%;110%/108%;115%/116%;125%/123.1%;140%/138.5%;150%/153.9%;180%/182%;200%/197%';
CKEDITOR.dtd.del = CKEDITOR.dtd.strike;
CKEDITOR.dtd.ins = CKEDITOR.dtd.u;
CKEDITOR.config.coreStyles_underline = { element : 'ins' };
CKEDITOR.config.coreStyles_strike = { element : 'del' };
CKEDITOR.config.dialog_backgroundCoverColor = 'rgb(55, 55, 55)';

// CKEDITOR.config.extraPlugins = '';
CKEDITOR.editorConfig = function(config)
{
	// config.skin = 'office2003';
	config.skin = 'nucleus';
	config.toolbar_nucleus =
	[
	    ['Source'],
	    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
	    ['PasteText','PasteFromWord'],
	    ['Undo','Redo','-','Find','-','RemoveFormat'],
	    '/',
	    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
	    ['JustifyLeft','JustifyCenter','JustifyRight'],
	    ['Link','Unlink','Anchor'],
	    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar'],
	    '/',
	    ['Format','Font','FontSize'],
	    ['TextColor','BGColor'],
	    ['Maximize', 'ShowBlocks','-','About']
	];
	config.toolbar_simple =
	[
	    ['Source','-','Undo','Redo'],['Bold','Strike'],['TextColor','BGColor'],
	    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
	    '/',
	    ['JustifyLeft','JustifyRight'],
	    ['Link','Unlink'],
	    ['Image','HorizontalRule','Smiley'],
	    ['Format'],
	    ['Maximize', '-','About']
	];
	config.toolbar_full =
	[
	    ['Source','-','Save','Preview','-','Templates'],
	    ['PasteText','PasteFromWord'],
	    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	    ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
	    '/',
	    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
	    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
	    ['JustifyLeft','JustifyCenter','JustifyRight'],
	    ['Link','Unlink','Anchor'],
	    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar'],
	    '/',
	    ['Styles','Format','Font','FontSize'],
	    ['TextColor','BGColor'],
	    ['Maximize', 'ShowBlocks','-','About']
	];
}
