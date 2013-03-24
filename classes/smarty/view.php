<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Acts as an object wrapper for HTML pages with embedded PHP, called "views".
 * Variables can be assigned with the view object and referenced locally within
 * the view.
 *
 *             of methods into a single class Smarty_Kohana_View (use a property
 *             $_view_type with values NULL or Smarty_Kohana_View::TYPE_SMARTY)
 *             and choose actions accordingly. You can then easily override the
 *             class if you want.
 *
 * @package    Smarty3
 * @author     Mr Anchovy
 * @copyright  (c) 2011 Mr Anchovy
 * @license    http://kohanaframework.org/license
 */
class Smarty_View extends Kohana_View
{
    /**
     * Directory containing Smarty.class.php
     */
    protected static $_smarty_path;
    /**
     * Smarty object to use for all templates
     */
    protected static $_smarty_prototype;


    private static function create_dir($path, $name = '')
    {
        if (file_exists($path)) {
            if (is_dir($path)) {
                throw new Kohana_Exception('Could not write to :name directory',
                    array('name' => $name));
            } else {
                throw new Kohana_Exception(':name path is a file',
                    array('name' => $name));
            }
        } else {
            try {
                mkdir($path);
            } catch (Exception $e) {
                throw new Kohana_Exception('Could not create :name directory',
                    array('name' => $name));
            }
            if (!is_writeable($path)) {
                throw new Kohana_Exception('Created :name directory but could not write to it',
                    array('name' => $name));
            }
        }
    }

    /**
     * Returns the smarty prototype object, creating it if necessary
     * @return   Smarty prototype object
     */
    public static function smarty_prototype()
    {
        // see if we need to set up the prototype smarty object
        if (self::$_smarty_prototype === NULL) {

            if (version_compare(Kohana::VERSION,'3.2','>=')) {
                $config = Kohana::$config->load('smarty');
            } else {
                $config = Kohana::config('smarty');
            }

            $smarty_class = Kohana::find_file('vendor', 'smarty/libs/Smarty.class');
            require_once($smarty_class);

            // save the location in case we have more than one Smarty version around
            self::$_smarty_path = realpath(dirname($smarty_class)) . DIRECTORY_SEPARATOR;

            // instantiate the prototype Smarty object
            $smarty = new Smarty;
            self::$_smarty_prototype = $smarty;

            // set up the prototype with options from config file
            foreach ($config->smarty_config as $key => $value) {
                $smarty->$key = $value;
            }

            // deal with config options that are not simple properties
            $smarty->php_handling = constant($config->php_handling);

            // add the path to the plugins for the located Smarty distro
            $smarty->addPluginsDir(self::$_smarty_path . 'plugins');
            // add views directories for all loaded modules (including Smarty3)
            $dirs = array();
            foreach (Kohana::modules() as $dir) {
                if (is_dir($dir.'/views'))
                {
                    $dirs[] = "{$dir}views";
                }
            }
            $smarty->addTemplateDir($dirs);

            if ($config->check_dirs) {
                // check we can write to the compiled templates directory
                if (!is_writeable($smarty->getCompileDir())) {
                    self::create_dir($smarty->getCompileDir(), 'Smarty compiled template');
                }

                // if smarty caching is enabled, check we can write to the cache directory
                if ($smarty->caching && !is_writeable($smarty->getCacheDir())) {
                    self::create_dir($smarty->getCacheDir(), 'Smarty cache');
                }
            }

            // now assign useful globals
            $smarty->assignGlobal('base_url', URL::base());
            $smarty->assignGlobal('helper', new Smarty_Helper);

            // register any globals
            foreach (self::$_global_data as $key => $value) {
                if (isset($bound[$key])) {
                    Smarty::$global_tpl_vars[$key] = new Smarty_variable();
                    Smarty::$global_tpl_vars[$key]->value = &$value;
                } else {
                    $smarty->assignGlobal($key, $value);
                }
            }
        }
        return self::$_smarty_prototype;
    }

    public static function capture($kohana_view_filename, array $kohana_view_data)
    {
        if(substr($kohana_view_filename,-3,3) != 'tpl')
        {
            return parent::capture($kohana_view_filename,$kohana_view_data);
        }


        //Now, for those smarty guys
        $t = Profiler::start('Smarty','Render '.Kohana_Debug::path($kohana_view_filename));

        $smarty = self::smarty_prototype();
        foreach($kohana_view_data as $key => $value)
        {
            $smarty->assign($key,$value);
        }

        foreach(View::$_global_data as $key => $value)
        {
            $smarty->assignGlobal($key,$value);
        }

        $tpl = $smarty->fetch($kohana_view_filename);
        $smarty->clearAllAssign();

        Profiler::stop($t);

        return $tpl;
    }

    public function set_filename($filename)
    {
        if(substr($filename, 0,7) == 'smarty:')
        {
            $tpl_name = substr($filename,7);
            if(strlen($tpl_name) == 0)
            {
                return $this;
            }
            return self::set_smarty_filename($tpl_name);
        }
        elseif(substr($filename,-3,3) == 'tpl')
        {
            return self::set_smarty_filename($filename);
        }
        return parent::set_filename($filename);
    }
    /**
     * Sets the view filename.
     *
     *     $view->set_filename($file);
     *
     * @param   string  view filename
     * @return  View
     * @throws  View_Exception
     */
    public function set_smarty_filename($file)
    {
        $file = substr($file,0,strlen($file)-4);
        if (($path = Kohana::find_file('views', $file,'tpl')) === FALSE)
        {
            throw new View_Exception('The requested view :file could not be found', array(
                ':file' => $file,
            ));
        }

        // Store the file path locally
        $this->_file = $path;

        return $this;
    }

} // End View_Smarty
