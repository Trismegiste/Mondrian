<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Refactor;

use Trismegiste\Mondrian\Visitor;
use Symfony\Component\Finder\SplFileInfo;
use Trismegiste\Mondrian\Parser\PackageParser;

/**
 * Contractor refactors a list of classes with annotations hints.
 * 
 * What it does ?
 *  * It creates a new interface for each class with annotation 
 *    like "@mondrian contractor NewInterfaceName".
 *  * it replaces all these classes by their new interface in 
 *    methods parameters (public or not, this is important)
 *  * it adds the inheritance for NewInterfaceName
 * 
 * Each interface is stored in the same namespace, neighbour of the
 * class in a directory. NewInterfaceName is a short name not a FQCN.
 * It is not possible to store the generated content in another directory 
 * since everybody uses Git or at least SVN. Therefore you can launch the
 * test suite immediatly.
 * 
 * It is a dumb refactoring but it makes the dull job to create new interfaces
 * by gathering public methods for each class in only one pass. There is no
 * name collision check or whatsoever.
 * 
 * The boring stage of sequences of ctrl-C/ctrl-V/ctrl-X is passed, 
 * now it is time to use your brain and think about domain, model, business 
 * and object contract :)
 *
 * Thereafter, you need to create a tree of contracts with these
 * 'not-really-abstract' interfaces. You need to put common contract in parent interface,
 * find common methods, remove unused methods, rename, move interfaces 
 * in other namespace etc... The perfect time to work with the digraph on the
 * second screen.
 * 
 * Note: All classnames are transformed in FQCN. It is not beautiful but 
 * actually, it is more useful than I thought : since these interfaces will
 * be splitted, renamed or moved, you don't have to think about "use" statements
 * and massive "search & replace" are made easier.
 * 
 */
class Contractor
{

    /**
     * Parse and refactor
     *  
     * @param \Iterator $iter list of absolute path to files to parse
     */
    public function refactor(\Iterator $iter)
    {
        $parser = new PackageParser(new \PHPParser_Parser(new \PHPParser_Lexer()));
        $context = new Refactored();
        // passes :
        // finds which class must be refactored (and add inheritance)
        $pass[0] = new Visitor\NewContractCollector($context);
        // replaces the parameters types with the interface
        $pass[1] = new Visitor\ParamRefactor($context);
        // creates the new interface file
        $pass[2] = new Visitor\InterfaceExtractor($context, array($this, 'writeStatement'));

        $stmts = $parser->parse($iter);

        foreach ($pass as $collector) {
            $traverser = new \PHPParser_NodeTraverser();
            $traverser->addVisitor($collector);
            $traverser->traverse($stmts);
        }
    }

    /**
     * write a content to a file
     * 
     * @param string $fch absolute path
     * @param array $stmts an array of PHPParser_Stmt
     */
    public function writeStatement($fch, array $stmts)
    {
        $prettyPrinter = new \PHPParser_PrettyPrinter_Default();
        file_put_contents($fch, "<?php\n\n" . $prettyPrinter->prettyPrint($stmts));
    }

}