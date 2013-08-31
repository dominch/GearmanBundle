<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Module;

use Mmoreram\GearmanBundle\Driver\Gearman\Job;
use Mmoreram\GearmanBundle\Driver\Gearman\Work;
use Symfony\Component\DependencyInjection\ContainerAware;
use ReflectionMethod;

/**
 * Job class
 * 
 * This class provide all worker definition.
 */
class JobClass extends ContainerAware
{

    /**
     * @var string
     * 
     * Callable name for this job
     * If is setted on annotations, this value will be used
     *  otherwise, natural method name will be used.
     */
    private $callableName;


    /**
     * @var string
     * 
     * Method name
     */
    private $methodName;


    /**
     * @var string
     * 
     * RealCallable name for this job
     * natural method name will be used.
     */
    private $realCallableName;


    /**
     * @var string
     * 
     * Description of Job
     */
    private $description;


    /**
     * @var integer
     * 
     * Number of iterations this job will be alive before die
     */
    private $iterations;


    /**
     * @var string
     * 
     * Default method this job will be call into Gearman client
     */
    private $defaultMethod;


    /**
     * @var array
     * 
     * Collection of servers to connect
     */
    private $servers;


    /**
     * Construct method
     *
     * @param Job              $methodAnnotation  MethodAnnotation class
     * @param ReflectionMethod $method            ReflextionMethod class
     * @param string           $callableNameClass Callable name class
     * @param array            $servers           Array of servers defined for Worker
     * @param array            $defaultSettings   Default settings for Worker
     */
    public function __construct( Job $methodAnnotation, ReflectionMethod $method, $callableNameClass, array $servers, array $defaultSettings)
    {
        $this->callableName = is_null($methodAnnotation->name)
                            ? $method->getName()
                            : $methodAnnotation->name;

        $this->methodName = $method->getName();

        $this->realCallableName = str_replace('\\', '', $callableNameClass . '~' . $this->callableName);
        $this->description  = is_null($methodAnnotation->description)
                            ? 'No description is defined'
                            : $methodAnnotation->description;


        $this->iterations   = is_null($methodAnnotation->iterations)
                            ? (int) $defaultSettings['iterations']
                            : $methodAnnotation->iterations;


        $this->defaultMethod    = is_null($methodAnnotation->defaultMethod)
                                ? $defaultSettings['method']
                                : $methodAnnotation->defaultMethod;


        /**
         * By default, this job takes default servers defined in its worker
         */
        $this->servers = $servers;


        /**
         * If is configured some servers definition in the worker, overwrites
         */
        if ($methodAnnotation->servers) {

            $this->servers  = ( is_array($methodAnnotation->servers) && !isset($methodAnnotation->servers['host']) )
                            ? $methodAnnotation->servers
                            : array($methodAnnotation->servers);
        }
    }

    /**
     * Retrieve all Job data in cache format
     *
     * @return array
     */
    public function toArray()
    {
        return array(

            'callableName'          =>  $this->callableName,
            'methodName'            =>  $this->methodName,
            'realCallableName'      =>  $this->realCallableName,
            'description'           =>  $this->description,

            'iterations'			=>  $this->iterations,
            'servers'               =>  $this->servers,
            'defaultMethod'         =>  $this->defaultMethod,
        );
    }
}
