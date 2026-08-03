//go:build !windows

package main

import "os/exec"

// En Linux o macOS no hay ventanas de consola que ocultar. Existe solo para
// poder compilar y probar el launcher fuera de Windows.
func hideWindow(cmd *exec.Cmd) {}
