Ext.onReady(function(){
	Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
	Ext.Ajax.request({
	   url: baseUrl+'index.php/sys/sys_menu/getMenuAjaxJs',
	   disableCaching: false,
	   success: function(responseObj){
	   		var menu = Ext.util.JSON.decode(responseObj.responseText);
   			var tb = new Ext.Toolbar({
				id: 'mainMenu',
				items: [menu.root]
		   	});
		   	tb.render('sys_menu');
	   },
	   failure: function(){
	   }
	});

	
});
