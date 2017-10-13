Ext.apply(Ext.form.VTypes, {
    password : function(val, field) {
        if (field.initialPassField) {
            var pwd = Ext.getCmp(field.initialPassField);
            return (val == pwd.getValue());
        }
        return true;
    },

    passwordText : 'No coinciden los Passwords'
});




Ext.onReady(function(){

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
 
    // Create a variable to hold our EXT Form Panel. 
    // Assign various config options as seen.	 
    var login = new Ext.FormPanel({ 
        labelWidth:115, 
        frame:true, 
        title:'Cambiar password', 
        defaultType:'textfield',
        monitorValid:true,
        // Specific attributes for the text fields for username / password. 
        // The "name" attribute defines the name of variables sent to the server.
        items:[{ 
            fieldLabel:'Usuario', 
            name:'user_name', 
            id:'user_name',
            allowBlank:false
        } ,{ 
            fieldLabel:'Nuevo Password', 
            name:'newpassword', 
            inputType:'password',
            id:'newpassword',
            invalidText: "La contrase&ntilde;a debe contener al menos un n&uacute;mero, una mayuscula y un caracter especial (#, $, %, @, *).",
            minLength:'8',
            maxLength:'30',
            allowBlank:false
                 
        }],
 
        // All the magic happens after the user clicks the button     
        buttons:[{ 
            text:'Aceptar',
            id: 'changeButton',
            formBind: true,	 
            listeners: {
                click: function(){
						   
                    //    var hash;
                    // var oldhash;
                    // hash=hex_md5(Ext.getCmp('newpassword').getValue());
                    //oldhash=hex_md5(Ext.getCmp('oldpassword').getValue());
                    //Ext.getCmp('newpassword').setValue(hash);
                    // Ext.getCmp('user_password_cfrm').setValue(hash);
                    // Ext.getCmp('oldpassword').setValue(oldhash);
                    //alert (hash);
                    if(login.getForm().isValid()){
				           
						    
                        login.getForm().submit({
                            url: baseUrl+'index.php/user/user_users/changePassword',
                            method: 'POST',
                            success: function(form, action){
                                Ext.MessageBox.show({
                                    title: 'Datos salvados correctamente',
                                    msg: 'Datos salvados correctamente',
                                    width: 300,
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.INFO
                                });
                            },
                            failure:function(form, action){ 
                                if(action.failureType == 'server'){ 
                                    obj = Ext.util.JSON.decode(action.response.responseText); 
                                    Ext.Msg.alert('Fallo al cambiar la contrase&ntilde;a!', obj.errors.reason); 
                                }else{ 
                                    Ext.Msg.alert('Advertencia!', 'Authentication server is unreachable : ' + action.response.responseText); 
                                } 
                                login.getForm().reset(); 
                            }                            
                        });
                    }
                }
            }                
                
        // Function that fires when user clicks the button 
        /* handler:function(){ 
                    login.getForm().submit({ 
                        method:'POST', 
                        waitTitle:'Conectando', 
                        waitMsg:'Enviando datos...',
 
			// Functions that fire (success or failure) when the server responds. 
			// The one that executes is determined by the 
			// response that comes from login.asp as seen below. The server would 
			// actually respond with valid JSON, 
			// something like: response.write "{ success: true}" or 
			// response.write "{ success: false, errors: { reason: 'Login failed. Try again.' }}" 
			// depending on the logic contained within your server script.
			// If a success occurs, the user is notified with an alert messagebox, 
			// and when they click "OK", they are redirected to whatever page
			// you define as redirect. 
 
                        success:function(){ 
                        	Ext.Msg.alert('Estado', 'Se ha modificado su contrase&ntilde;a', function(btn, text){
							   if (btn == 'ok'){
					                        var redirect = baseUrl+'index.php/welcome';
					                        window.location = redirect;
			                   }
			        		});
                        },
 
			// Failure function, see comment above re: success and failure. 
			// You can see here, if login fails, it throws a messagebox
			// at the user telling him / her as much.  
 
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
                } */
        },{ 
            text:'Cancelar',
            handler:function(){	 
                var redirect = baseUrl+'index.php/welcome'; 
                window.location = redirect;
            }
        }] 
    });
 
 
    // This just creates a window to wrap the login form. 
    // The login object is passed to the items collection.       
    var win = new Ext.Window({
        layout:'fit',
        width:320,
        height:180,
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