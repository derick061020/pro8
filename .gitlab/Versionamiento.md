
## Tag de versión (actualmente v8.2.0)

```bash
v1.4.2
 │ │ └── PATCH: bug fixes, sin romper nada
 │ └──── MINOR: nueva funcionalidad, compatible
 └────── MAJOR: cambios que rompen compatibilidad
```

## Estructura de los commits 

```bash
git commit -n "fix(module) : Bug"
                    │         │
                    │         └────── Mensaje descriptivo sobre la solución que hace el commit
                    └────── Modulo afectado de la solución de los bugs
```

## PATCH, Intervalo cada 2 semanas a 3 semanas respecto a la catnidad de issues que haya

```bash
git tag -a v8.0.{x}
git push origin v8.2.{x}
```

## RELEASE, Commit que tiene una nueva funcionalidad

```bash
git tag -a v8.{y}.0
git push origin v8.{y}.{x}
```

Esto se realizaría cada mes donde se crearia un nuevo tag con su comentario adicional sobre las nuevas característica, tiene que seguir la estructura de los commits para diferenciar los bugs con las features

Para ver el tag actual donde se encuentrá el facturador ejecutar lo siguiente:

```bash
git describe --tags --abbrev=0
```