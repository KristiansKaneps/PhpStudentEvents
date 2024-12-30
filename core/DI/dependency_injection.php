<?php
declare(strict_types=1);

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'Dependency.php');

/**
 * Resolves and returns an instance of the given dependency class.
 * @template T
 * @param class-string<T> $dependencyClass The fully qualified class name to resolve.
 * @param T $override The instance that should be resolved in successive calls to this function (or `null` if it should
 *                    not be overridden, or `false` if it should be reset).
 * @return T The resolved instance of the given class.
 * @throws InvalidArgumentException If the class does not implement \DI\Dependency.
 */
function resolve(string $dependencyClass, mixed $override = null): \DI\Dependency {
    static $dependencyInstances = [];

    if ($override === false) unset($dependencyInstances[$dependencyClass]);
    else if (!empty($override)) $dependencyInstances[$dependencyClass] = $override;

    if (isset($dependencyInstances[$dependencyClass]))
        return $dependencyInstances[$dependencyClass];

    if (!is_subclass_of($dependencyClass, \DI\Dependency::class)) {
        throw new InvalidArgumentException(
            "Class $dependencyClass must implement " . \DI\Dependency::class
        );
    }

    // Use Reflection to resolve constructor dependencies.
    try {
        $reflectionClass = new ReflectionClass($dependencyClass);
        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            // No constructor, instantiate directly.
            $instance = new $dependencyClass();
        } else {
            // Resolve constructor parameters recursively.
            $parameters = $constructor->getParameters();
            $dependencies = array_map(function (ReflectionParameter $parameter) use ($dependencyClass) {
                $dependencyType = $parameter->getType();
                if (!$dependencyType instanceof ReflectionNamedType || $dependencyType->isBuiltin()) {
                    throw new InvalidArgumentException(
                        "Cannot resolve parameter '{$parameter->getName()}' for class '$dependencyClass'. " .
                        "Ensure it is a class implementing \DI\Dependency."
                    );
                }
                $dependencyClassName = $dependencyType->getName();
                return resolve($dependencyClassName);
            }, $parameters);
            $instance = $reflectionClass->newInstanceArgs($dependencies);
        }
    } catch (ReflectionException $e) {
        throw new Error("Reflection exception: " . $e->getMessage());
    }

    $dependencyInstances[$dependencyClass] = $instance;
    return $instance;
}

function findArgumentValueInArgumentMap(null|array $argumentMap, \ReflectionParameter $parameter): mixed {
    if (!empty($argumentMap)) {
        $parameterName = strtolower(str_replace('_', '', $parameter->getName()));
        foreach ($argumentMap as $argumentName => $argumentValue) {
            if (strtolower(str_replace('_', '', $argumentName)) !== $parameterName)
                continue;
            return $argumentValue;
        }
    }
    if ($parameter->isDefaultValueAvailable()) {
        return $parameter->getDefaultValue();
    }
    throw new InvalidArgumentException(
        "Cannot resolve parameter '{$parameter->getName()}'. Ensure it is a class implementing '" . \DI\Dependency::class . "' or pass it via `argumentMap`."
    );
}

/**
 * Invokes a function with resolved dependencies.
 * @param callable-string|callable $function The function name to invoke.
 * @param null|array $argumentMap If a dependency is not found, this map will be checked for placeholder.
 * @return mixed The result of the invoked function.
 * @throws InvalidArgumentException If the function cannot be invoked, or the dependencies cannot be resolved.
 */
function resolveAndCallFunction(string|callable $function, null|array $argumentMap = null): mixed {
    if (is_string($function) && !function_exists($function)) {
        throw new InvalidArgumentException("Function '$function' does not exist.");
    }

    // Use Reflection to resolve function dependencies.
    try {
        // Resolve function argument types and inject them as needed.
        $reflectionFunction = new \ReflectionFunction($function);
        $parameters = $reflectionFunction->getParameters();
        $resolvedParameters = array_map(function (\ReflectionParameter $parameter) use ($argumentMap) {
            $parameterType = $parameter->getType();
            if (!($parameterType instanceof \ReflectionNamedType)) {
                throw new InvalidArgumentException(
                    "Cannot resolve parameter '{$parameter->getName()}'. Ensure it is a class implementing '" . \DI\Dependency::class . "' or pass it via `argumentMap`."
                );
            }
            try {
                if ($parameterType->isBuiltin()) {
                    return findArgumentValueInArgumentMap($argumentMap, $parameter);
                } else {
                    $dependencyClass = $parameterType->getName();
                    return resolve($dependencyClass);
                }
            } catch (InvalidArgumentException) {
                return findArgumentValueInArgumentMap($argumentMap, $parameter);
            }
        }, $parameters);
        return $reflectionFunction->invokeArgs($resolvedParameters);
    } catch (ReflectionException $e) {
        throw new Error("Reflection exception: " . $e->getMessage());
    }
}

/**
 * Invokes a method with resolved dependencies.
 * @param string $method The method name to invoke.
 * @param object $instance The instance that this method belongs.
 * @param null|array $argumentMap If a dependency is not found, this map will be checked for placeholder.
 * @returns mixed The result of the invoked method.
 * @throws InvalidArgumentException If the method cannot be invoked, or the dependencies cannot be resolved.
 */
function resolveAndCallMethod(string $method, object $instance, null|array $argumentMap = null): mixed {
    if (!method_exists($instance, $method)) {
        throw new InvalidArgumentException("Method '$method' does not exist in class " . $instance::class . ".");
    }

    // Use Reflection to resolve method dependencies.
    try {
        // Resolve method argument types and inject them as needed.
        $reflectionMethod = new \ReflectionMethod($instance, $method);
        $parameters = $reflectionMethod->getParameters();
        $resolvedParameters = array_map(function (\ReflectionParameter $parameter) use ($argumentMap) {
            $parameterType = $parameter->getType();
            if (!($parameterType instanceof \ReflectionNamedType)) {
                throw new InvalidArgumentException(
                    "Cannot resolve parameter '{$parameter->getName()}'. Ensure it is a class implementing '" . \DI\Dependency::class . "' or pass it via `argumentMap`."
                );
            }
            try {
                if ($parameterType->isBuiltin()) {
                    return findArgumentValueInArgumentMap($argumentMap, $parameter);
                } else {
                    $dependencyClass = $parameterType->getName();
                    return resolve($dependencyClass);
                }
            } catch (InvalidArgumentException) {
                return findArgumentValueInArgumentMap($argumentMap, $parameter);
            }
        }, $parameters);
        return $reflectionMethod->invokeArgs($instance, $resolvedParameters);
    } catch (ReflectionException $e) {
        throw new Error("Reflection exception: " . $e->getMessage());
    }
}
