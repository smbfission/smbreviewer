<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* filter.twig */
class __TwigTemplate_dea09e44a0f5ff48320b68886838332403b60055f991f0d22325e6e1b6451551 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<fieldset id=\"tableFilter\">
    <legend>";
        // line 2
        echo _gettext("Filters");
        echo "</legend>
    <div class=\"formelement\">
        <label for=\"filterText\">";
        // line 4
        echo _gettext("Containing the word:");
        echo "</label>
        <input name=\"filterText\" type=\"text\" id=\"filterText\"
               value=\"";
        // line 6
        echo twig_escape_filter($this->env, ($context["filter_value"] ?? null), "html", null, true);
        echo "\">
    </div>
</fieldset>
";
    }

    public function getTemplateName()
    {
        return "filter.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  50 => 6,  45 => 4,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "filter.twig", "/home/sites/13b/4/4e3fc12a82/reviews.smbreviewer.com/uploads/temp/pa/templates/filter.twig");
    }
}
