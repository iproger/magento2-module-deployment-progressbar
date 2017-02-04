<?php

namespace Iproger\DeploymentProgressBar\Helper\ProgressBar;

/**
 * Cli ProgressBar
 *
 * @author Mykhailo Shatilov <shatilov@usa.com>
 */
class Manager extends \ProgressBar\Manager
{

    /**
     * Printer
     */
    protected $printer;

    /**
     * {@inheritdoc}
     */
    public function __construct(
    $current, $max, $width = 80, $doneBarElementCharacter = '=',
    $remainingBarElementCharacter = '-', $currentPositionCharacter = '>')
    {
        parent::__construct(
            $current, $max, $width, $doneBarElementCharacter,
            $remainingBarElementCharacter, $currentPositionCharacter
        );

        $this->format = <<<EOF
%current%/%max% [%bar%] %percent%%
EOF;

        $this->addReplacementRule('%percent%', 30,
            function ($buffer, $registry) {
            $value = ($registry->getValue('current') * 100) / $registry->getValue('max');
            return round($value);
        });
    }

    /**
     * Prints the progress bar
     *
     * @param boolean $lineReturn
     */
    protected function display($lineReturn)
    {
        $buffer = '';
        $buffer = $this->format;

        foreach ($this->replacementRules as $priority => $rule) {
            foreach ($rule as $tag => $closure) {
                $buffer = str_replace($tag, $closure($buffer, $this->registry),
                    $buffer);
            }
        }

        $buffer = $this->clearRightCharacters($buffer);

        $eolCharacter = ($lineReturn) ? "\n" : "\r";
        $this->printer->write("$buffer$eolCharacter");
    }

    /**
     * {@inheritdoc}
     */
    public function update($current)
    {
        try {
            parent::update($current);
        } catch (\InvalidArgumentException $ex) {
            
        }
    }

    /**
     * Set printer
     * TODO: add interface
     * 
     * @param type $printer
     */
    public function setPrinter($printer)
    {
        $this->printer = $printer;
    }

    /**
     * Get printer
     */
    public function getPrinter()
    {
        return $this->printer;
    }
}
