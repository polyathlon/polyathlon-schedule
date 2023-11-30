(function() {
    tinymce.PluginManager.add('poly_tc_buttons', function( editor, url ) {
        var menuItems = new Array();
        for (var i = 0; i < poly_shortcodes.length; i++){
            var shortcode = poly_shortcodes[i];

            item = {
               text: shortcode.title,
               value: shortcode.shortcode,
               onclick: function() {
                   editor.insertContent(this.value());
               }
            };
            menuItems.push(item);
        }

        editor.addButton( 'poly_insert_tc_button', {
            text: 'Polyathlon Schedule',
            icon: 'icon dashicons-before dashicons-schedule',
            type: 'menubutton',
            menu: menuItems
        });
    });
})();