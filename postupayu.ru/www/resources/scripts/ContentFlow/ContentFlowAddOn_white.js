/*  ContentFlowAddOn_white, version 2.0 
 *  (c) 2008 - 2010 Sebastian Kutsch
 *  <http://www.jacksasylum.eu/ContentFlow/>
 *
 *  This file is distributed under the terms of the MIT license.
 *  (see http://www.jacksasylum.eu/ContentFlow/LICENSE)
 */

new ContentFlowAddOn ('white', {

    init: function () {
        this.addStylesheet();
    },
	
    ContentFlowConf: {
        reflectionColor: "#ffffff", // none, transparent, overlay or hex RGB CSS style #RRGGBB
        circularFlow: false,

        scaleFactor: 1.1,               // overall scale factor of content

        reflectionHeight: 0.2,          // float (relative to original image height)

        onclickActiveItem: function (item){
            var url = item.content.getAttribute('href');
            if(url){
                window.location.href = url;
            }
        }

    }

});
