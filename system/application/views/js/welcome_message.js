var newsDataStore;
var array;

Ext.onReady(function() {

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('News');
    

   	var xg = Ext.grid;

	var p = new Ext.Panel({
        title: 'Noticias',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
		
		bodyCfg: {
	    }
    });
    
    /*
     * Definimos el registro para un center
     */
     
    News.newsRecord = new Ext.data.Record.create([
        {name: 'new_id', type: 'int'},
        {name: 'dateput'},
		{name: 'content', type: 'string'},
		{name: 'name', type: 'string'}
		//,{name: 'priority', type: 'int'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    News.newsGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'new_id'},
        News.newsRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    News.newsDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/newsAdmin/setDataGrid',
        method: 'POST'
    });

    newsDataStore = new Ext.data.Store({
        id: 'new_id',
        proxy: News.newsDataProxy,
        reader: News.newsGridReader        
    });


 /*News.resultTpl = new Ext.XTemplate(
        '<tpl for=".">',
        '<div class="search-item">',
            '<h3><span>{lastPost:date("M j, Y")}<br />by {author}</span>',
            '<a href="http://extjs.com/forum/showthread.php?t={topicId}&p={postId}" target="_blank">{title}</a></h3>',
            '<p>{excerpt}</p>',
        '</div></tpl>'
    );

*/
    News.resultTpl = new Ext.XTemplate(
        '<tpl for=".">',
        '<div class="search-item">',
            '<h2>',
            '<u><font color="#000066"><strong>{name}</strong></font></u></h2>',
            '<p><h2>{content}</h2></p>',
        '</div></tpl>'
    );

   News.panel = new Ext.Panel({
        height:500,
		width:750,
        autoScroll:true,

        items: new Ext.DataView({
            tpl: News.resultTpl,
            store: newsDataStore,
            itemSelector: 'div.search-item' //el de resultTpl
        }),

       

        bbar: new Ext.PagingToolbar({
            store: newsDataStore,
            pageSize: 5,
            displayInfo: true,
            displayMsg: 'Documentos {0} - {1} of {2}',
            emptyMsg: "No hay noticias a mostrar"
        })
    });

    News.panel.render(Ext.get('welcom_grid'));
    newsDataStore.load({params: {start:0,limit:5}});
});
