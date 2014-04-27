## Smarty3 Module for Kohana

This is a module for the [Kohana PHP framework](http://kohanaphp.com/) that
integrates the [Smarty Template Engine](http://www.smarty.net/).

Documentation and support for this module can be found on
[Github](https://github.com/MrAnchovy/Kohana_Smarty3).
Note this version is compatible with Kohana 3.3. For Kohana 3.0-3.2 you want the
[1.x branch](https://github.com/MrAnchovy/Kohana_Smarty3/tree/1.x-for-Kohana-3.0-3.2).

**Version 2.0.3-dev**

### Quick Start

- Download and unpack the module
- Put the smarty3 module in your modules directory
- Enable the smarty3 module in your bootstrap file
- To use Smarty for a view you need to create a Smarty template
  `template_name.tpl` in your application's views directory. Then when you do
  `$view = View::factory('template_name.tpl')` you can use the $view object
  just as you would with a regular PHP view.
- That's all there is to it! There's only one hitch, you can't use
  `$view = new View('template_name.tpl')` or change the template of an existing
  PHP view object to a Smarty template with `$view->set_filename()`.
- If your controller extends the `Controller_Template` class, you can use a
  Smarty template for your page layout too - just set
  `public $template = 'layout_template_name.tpl';` in your controller class.

### Upgrading from earlier Version 2 releases

- Download and unpack the module
- Replace the existing smarty3 module with the new version
- Delete compiled templates (in `APPPATH/cache/smarty_compiled` by default)

### Upgrading from older versions

Upgrading from older versions should work as above.

### How does this magic work?

The Smarty module creates a `View` class that extends `Kohana_View` to intercept
the `View::factory()` method and return an instance of either the unmodified
`Kohana_View` for a PHP template or an instance of `Smarty_View` if the view file
has the `.tpl` extension. `Smarty_View` implements the methods of `Kohana_View` to
act on a Smarty object. If you are converting existing code, or just prefer to
use the Smarty object directly, you can access it with `$view->smarty()`, but
note that not all of the available methods have been tested and may produce
unpredicatable effects.

### More information

Documentation and support for this module can be found on
[Github](https://github.com/MrAnchovy/Kohana_Smarty3).
Support for Smarty and its standard plugins is of course on the
[Smarty](http://www.smarty.net) site.

### Copyright

* The Smarty module is Copyright © 2009-14 [Mr Anchovy](http://www.mranchovy.com/).
* Kohana is Copyright © 2007-2014 [Kohana Team](http://kohanaframework.org/).  
* Smarty is Copyright © 2001-2014 [New Digital Group, Inc.](http://www.smarty.net/).

### Licence

This distribution of the Smarty3 Module for Kohana is released under the
[Kohana License](http://kohanaframework.org/license)

The distribution of Smarty included in this package is released under the
GNU Lesser General Public License (LGPL) - see the Smarty documentation for
more details.
