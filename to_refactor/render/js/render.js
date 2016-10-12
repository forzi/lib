Render = function(arguments_file) {
    var webPage = require('webpage');
    
    this._page = webPage.create();
    this._time_start = 0; //new Date();
    this._time_end = 0;
    this._unique = '';
    this._types = ['pdf', 'gif', 'jpg', 'jpeg', 'png', 'html', 'txt'];
    this._tmp_path = 'tmp/';
    this._cur_path = '';
    this._include_path = 'includes/';
    this._render_path = 'render/';
    this._fso = require('fs');
    this._colontitles = {};
    this._col_types = ['header', 'footer'];
    
    this.time = 0;
    this.arguments_file = arguments_file;
    this.arguments = null;
    this.type = null;
    this.result = null;
    this.error = '';
    this.timeout = 30000;
}
Render.prototype = {
    render: function() {
        var content;
        var environment = this;
        
        this._time_start = new Date();
        this._unique = this.arguments_file.split('/').pop().split('.').shift();
        this._cur_path = this._tmp_path + this._unique + '/';
        
        this.arguments = this._fso.read(this.arguments_file);
        if ( !this.arguments ) {
            this.error = 'source file not found';
            this._show_result();
        }
        this.arguments = this._fso.read(this.arguments_file);
        //this.arguments = JSON.parse('"' + this.arguments + '"'); // magic!
        this.arguments = JSON.parse(this.arguments);
        this.type = this.arguments['type'];
        this.timeout = this.arguments['timeout'] * 1000;
        
        window.setTimeout(function () {
            environment.error = 'timeout ' + this.arguments['timeout'] + 's';
            environment._show_result();
        }, this.timeout);
        
        if ( this._types.indexOf(this.type) == -1 ) {
            this.error = 'such type doesn\'t supports';
            this._show_result();
        }
        
        if ( typeof this.arguments['content'] !== undefined ) {
            content = this.arguments['content'];
        } else {
            this.error = 'nothing to render';
            this._show_result();
        }
        
        this._remove_tmp();
        if ( !this._fso.makeDirectory(this._cur_path) ) {
            this.error = 'can\'t create tmp directory';
            this._show_result();
        }
        
        if ( !this._prepare_colontitles() ) {    
            this._show_result();
        }
        
        if ( !this._prepare_tmp_files() ) {
            this._show_result();
        }
        
        this._add_colontitles();
        this.copy_parameters(this._page, this.arguments['page']['page_setup']);
        
        this.page_render(content);
        
        return true;
    },
    page_render: function(content) {
        var environment = this,
            to_load = {};

        environment._page.onConsoleMessage = function(msg) {
            console.log('CONSOLE: ' + msg );
        };

        environment._page.onCallback = function(data) {
            for (key in data) {
                to_load[key] = data[key];
               }
            return true;
        };
        
        if ( content['headers'] !== undefined ) {
            environment._page.customHeaders = content['headers'];
        }
        
        environment._page.open(content['url'], function(status) {
            if (status !== 'success') {
                environment.error = 'can\'t render';    
                environment._show_result();
            }

            for (key in to_load) {
                // loading and execution script on page
                console.log('LOAD: ' + to_load[key]);
                environment._page.includeJs(to_load[key]);
            }

            window.setTimeout(function () {
                var result_file = environment._render_path + environment._unique + '.' + environment.type;
    
                environment._page.evaluate(function() {
                    //put code to execution
                });
                
                if ( environment.type == 'html' ) {
                    environment._fso.write(result_file, environment._page.content, 'b');
                } else if ( environment.type == 'txt' ) {
                    environment._fso.write(result_file, environment._page.plainText, 'b');
                } else if ( !environment._page.render(result_file) ) {
                    environment.error = 'can\'t render';
                    environment._show_result();
                }
                
                environment.result = result_file;
                environment._show_result();
                
            }, 200);
        });
    },
    copy_parameters: function(to, from) {
        for ( var key in from ) {
            to[key] = from[key];
        }
    },
    _remove_tmp: function() {
        this._fso.removeTree(this._tmp_path + this._unique);    
    },
    _prepare_colontitles: function() {
        for ( var key in this._col_types ) {
            var col_type = this._col_types[key];
            
            if ( this.arguments['page'][col_type] ) {
                for ( var cur_col_type in this.arguments['page'][col_type] ) {
                    this._fso.write(this._cur_path + col_type, Base64.decode(this.arguments['page'][col_type][cur_col_type]), 'b');
                    this._colontitles[col_type] = {};
                    this._colontitles[col_type][cur_col_type] = this._fso.read(this._cur_path + col_type);
                }
            }
        }
        return true;
    },
    _prepare_tmp_files: function() {
        this._fso.copyTree(this._include_path, this._cur_path);
        
        if ( typeof this.arguments['content']['url'] === undefined ) {
            var body_content = Base64.decode(this.arguments['content']['body']);
            var path = this._cur_path + this._unique + '.html';
            body_content = body_content.replace(new RegExp('##files_path##', 'g'), this._cur_path);    
            this._fso.write(path, body_content, 'b');
            this.arguments['content']['url'] = path;
        }
        
        for ( var file_name in this.arguments['resources'] ) {
            this._fso.write(this._cur_path + file_name, Base64.decode(this.arguments['resources'][file_name]), 'b')
        }
        
        return true;
    },
    _add_colontitles: function() {
        for ( var key in this._col_types ) {
            var col_type = this._col_types[key];

            if ( this._colontitles[col_type] ) {
                var files_path = this._cur_path,
                obj = this.arguments,
                colontitle = obj.parameters.paperSize[col_type] = {},
                spec = this._colontitles[col_type],
                main = {'cont' : spec.main || "" },
                even = spec.even ? {'cont' : spec.even} : main, 
                odd = spec.odd ? {'cont' : spec.odd} : main,
                first = spec.first ? {'cont' : spec.first} : odd;
                
                colontitle.height = (typeof obj[col_type].height !== undefined) ? obj[col_type].height : '15mm';
                
                colontitle.contents = phantom.callback(function(current_page, total_pages) {
                    var html = "";
                    
                    if (main.cont || even.cont || odd.cont || first.cont) {
                        if ( current_page == 1 ) { html = first.cont; }
                        else { html = (current_page % 2) ? odd.cont : even.cont ; }
                        
                        if (html) {
                            html = html.replace(new RegExp('##files_path##', 'g'), files_path);
                            html = html.replace(new RegExp('##current_page##', 'g'), current_page);
                            html = html.replace(new RegExp('##total_pages##', 'g'), total_pages);
                        }
                    }
                    return html;
                });
            }
        }
    },
    _show_result: function() {
        this._remove_tmp();
        this._time_end = new Date();
        this.time = this._time_end - this._time_start  + ' ms';
        console.log('time: ' + this.time);
        if ( render.error == '' ) {
            console.log(this.result);
            phantom.exit();
        } else {
            console.log('ERROR! ' + render.error);
            phantom.exit(198);
        }
    }
}

if ( !phantom.injectJs('base64.js') ) {
    console.log('can\'t load base64');
    phantom.exit(198);
}

var system = require('system');
var render = new Render(system.args[1]);

render._tmp_path = phantom.libraryPath + '/../tmp/';
render._render_path = phantom.libraryPath + '/../render/';
render._include_path = phantom.libraryPath + '/../includes/';

render.render();
