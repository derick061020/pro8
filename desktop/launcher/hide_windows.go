//go:build windows

package main

import (
	"os/exec"
	"syscall"
)

// hideWindow evita que cada proceso hijo (MariaDB, php-cgi, Caddy) abra su
// propia ventana de consola. Sin esto el usuario ve tres pantallas negras al
// arrancar y es casi seguro que cierre alguna sin querer.
func hideWindow(cmd *exec.Cmd) {
	cmd.SysProcAttr = &syscall.SysProcAttr{
		HideWindow:    true,
		CreationFlags: 0x08000000, // CREATE_NO_WINDOW
	}
}
