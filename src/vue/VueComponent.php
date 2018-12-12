<?php
namespace gibamaranhao\vue;

class VueComponent
{
  /**
   * Components Registry. A list of full path of the component file
   * @var array
   */
  private $components = [];
  /**
   * The prefix of the template Id
   * @var string
   */
  public $templatePrefix = 'vapp__';

  /**
   * Register components by giving the folder for recursive loading
   * @param  string $componentsDir full path of components folder
   * @return null
   */
  public function registerComponentsFromDir($componentsDir)
  {
    $this->components = array_merge($this->components,$this->getAllFiles($componentsDir));
  }

  /**
   * Register one or more components
   * @param  array|string $components Components list or single component full path
   * @return null
   */
  public function registerComponents($components)
  {
    if(is_array($components)) {
      $this->components = array_merge($this->components,$components);
    }else if(is_string($components)) {
      $this->components[] = $components;
    }
  }

  /**
   * Get the content of a file
   * @param  string $file full path of file
   * @return string       File content
   */
  public function loadFile($file)
  {
    return file_get_contents($file);
  }

  /**
   * Render a component
   * @param  string|int $key       A key for templateId concatenation
   * @param  string $component Component filename
   * @return string            Html result
   */
  public function renderComponent($key, $component)
  {
    $id = $this->generateId($key);
    $script = $this->loadFile($component);

    $template = $this->getTemplate($id, $script);
    $js = $this->getJs($id, $script);
    $css = $this->getCss($script);

    $html = $template.$js.$css;
    return $html;
  }

  /**
   * Render all components in the registry
   * @return string the Html result
   */
  public function renderAllComponents()
  {
    $html = '';
    foreach ($this->components as $key => $value) {
      $html .= $this->renderComponent($key, $value);
    }
    return $html;
  }

  /**
   * Get the template part from the component content
   * @param  string $id     The template Id
   * @param  string $script The component content
   * @return string         The template tag
   */
  public function getTemplate($id, $script)
  {
    preg_match('/<template[^<>]{0,}>(.*)<\/template[^<>]{0,}>/ms',$script, $matches);
    $tpl = isset($matches[1]) ? trim($matches[1]) : null;
    return '<template id="'.$id.'">'.$tpl.'</template>';
  }

  /**
   * Get the javascript part from the component content
   * @param  string $id     The template Id
   * @param  string $script The component content
   * @return string         The script tag
   */
  public function getJs($id, $script)
  {
    preg_match('/<script[^<>]{0,}>(.*)<\/script[^<>]{0,}>/ms',$script, $matches);
    $js = isset($matches[1]) ? trim($matches[1]) : null;
    $pos = strpos($js,'{');
    $js = substr($js,0,$pos+1)."template: '#$id',".substr($js,$pos+1);
    return '<script>'.$js.'</script>';
  }

  /**
   * Get the Style part of component content
   * @param  string $script The component content
   * @return string|null         The style tag
   */
  public function getCss($script)
  {
    preg_match('/<style[^<>]{0,}>(.*)<\/style[^<>]{0,}>/ms',$script, $matches);
    $style = isset($matches[1]) ? trim($matches[1]) : null;
    if(!$style)return null;
    return "<style>$style</style>";
  }

  /**
   * Generates the Id of template
   * @param  string|int $key The key of component
   * @return string      The template Id
   */
  public function generateId($key)
  {
    return $this->templatePrefix.$key;
  }

  /**
   * Look recursively in folder getting the full path of files
   * @param  string $dir Folder path
   * @return array      The of files inside the folder
   */
  public function getAllFiles($dir)
  {
    $list = scandir($dir);
    $ret = [];
    foreach ($list as $file) {
      if($file == '.' || $file == '..') continue;
      if(is_dir("$dir/$file")) {
        $ret = array_merge($ret, $this->getAllFiles("$dir/$file"));
      }else {
        $ret[] = "$dir/$file";
      }
    }
    return $ret;
  }

}
