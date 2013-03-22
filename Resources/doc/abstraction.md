# Abstraction of a concrete class

## What
These are two commands to help your refactoring.

## Analy[s|z]e
First you need to check the impact of the class

    app/console kandinsky:analyze My\Full\Qualified\Class\Name ./mess/directory

## Generate
Then you need to generate some classes :

    app/console kandinsky:generate My\Full\Qualified\Class\Name


This tool is a generator. You provide one concrete class and it creates 3 files :
* 1 interface with the methods of the concrete class (aka the contract)
* 1 abstract class which inherits the interface but not implements it and
implements only the constructor (aka the injector, usefull for SOA)
* 1 refactored class which inherits the abstract class

"Ok I understand the contract but what is this injector ?"
The role of this class is to inject parameters into the concrete class.
As you know the Dependency Inversion Principle : don't call us, we call you.
This abstract class is the prototype for a new service. It declares the contract
and declares what it needs for builing this service, but not implements anything.

In SOA, it is very usefull.

If you don't need/like it, you can only keep the interface and add inheritance
in the original concrete class.

### Interface Segregation Principle
As you know ISP, you know this new interface is not really usefull. Ok now
the implementation is decoupled from the contract but all methods are highly
coupled. The third step is to explode the interface into smaller interfaces
(My personal guidance is "No more than 5 methods per interface", but it is mine)

For this step, you can use the second provided tool by the bundle : the analyzer

It will parse and collects all links to the concrete class accross a set of files
Warning : it is not magic. It only detects method signatures and calls with the
same name as the concrete class methods. I pretend it is more user-friendly than a "grep"
but no more intelligent. Beside, it can't detect method calls with call_user_func
nor magic __call. In these cases, a grep could be a double check.

By analyzing the report, you can choose how you explode the new interface into
smaller ones. For that, I cannot write a tool, it is too specific, sorry.

### Replacing the concrete class by the interface
For this, you have to replace all occurences listed in the report by the right
interface you have created. Perhaps, I will create a tool for that.

### Done
Now you have achieved LSP, ISP and some DIP, you can start with SRP and later OCP.
Good luck !

## FAQ
### I really don't see the point of the "injector"
Well it is a matter of taste. Unlike Java, the SOA in PHP use "rich" constructor.
I think the constructor is enough important to possess it's own class.
My personal rule is : "If a parameter is mandatory to a class, don't write a
setter, put it in the constructor". It is much simplier and you don't have
to write many setters, checking if all properties are set before calling
other methods etc. Straightful.

### Refactoring a package must be done in one pass, not class by class
That's right. For example, the generated interface can be highly coupled to concrete
classes, which is very bad. Interfaces must depend on interfaces.
But if you're working on a dirty source code in emergency mode, with very light unit tests,
I prefer to refactor, one class at a time, in an iteration process.
Beside, you can analyze a set of classes before starting the refactoring.

That's why the first emergency mesure is to abstract classes, everywhere. With
this you can force the typing in method signature, force further inheritance,
and force your colleagues to work with interface. But of course, it is not sufficient,
only the first step.

## TODO

This tool will not improve as I'm working on a totally new approch with
the graph theory : stay in touch