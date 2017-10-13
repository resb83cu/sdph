Ext.onReady(function(){
	
    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
 
	// Create a variable to hold our EXT Form Panel. 
	// Assign various config options as seen.	 
    var login = new Ext.FormPanel({ 
        labelWidth:80,
        url: baseUrl+'index.php/sys/system/login/TRUE', 
        frame:true, 
        title:'Registrarse', 
        defaultType:'textfield',
        items:[{ 
                fieldLabel:'Usuario', 
                name:'loginUsername'
            },{
                fieldLabel:'Contrase&ntilde;a',
                name:'loginPassword',
                inputType:'password',
				listeners: {
				    specialkey: function(field, el){
				        if (el.getKey() == Ext.EventObject.ENTER)
				            Ext.getCmp('loginButton').fireEvent('click')
				    }
				}
                
            }],
 
	// All the magic happens after the user clicks the button     
        buttons:[{ 
                text:'Ingresar',
                id: 'loginButton',
                formBind: true,
				listeners: {
				    click: function(){
				           if(login.getForm().isValid()){
				            login.getForm().submit({
				                url: baseUrl+'index.php/sys/system/login/TRUE',
				                method: 'POST',
				                success: function(form, action){
				                    var redirect = baseUrl+'index.php/welcome'; 
			                        window.location = redirect;
				                },
				                failure:function(form, action){ 
		                            if(action.failureType == 'server'){ 
		                                obj = Ext.util.JSON.decode(action.response.responseText); 
		                                Ext.Msg.alert('Fall&oacute; el registro!', obj.errors.reason); 
		                            }else{ 
		                                Ext.Msg.alert('Advertencia!', 'Authentication server is unreachable : ' + action.response.responseText); 
		                            } 
		                            login.getForm().reset(); 
		                        }                            
				            });
				        }
				    }
				}
            },{ 
                text:'Cancelar',
                handler:function(form, e){	 
	                var redirect = baseUrl+'index.php/welcome'; 
	                window.location = redirect;
                }
            }] 
    });
 
 
	// This just creates a window to wrap the login form. 
	// The login object is passed to the items collection.       
    var win = new Ext.Window({
        layout:'fit',
        width:300,
        height:150,
        closable: false,
        resizable: false,
        modal: true,
        plain: true,
        border: false,
        items: [login]
	});
	login.render(document.getElementById("formExt"));
	win.show();
	
});
