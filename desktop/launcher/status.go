package main

import (
	"encoding/json"
	"fmt"
	"os"
)

// SyncStatus refleja el archivo que escribe el demonio de sincronización
// (Modules\Offline\Services\StatusSnapshot). Si allá cambia el formato, hay
// que cambiarlo también acá.
type SyncStatus struct {
	Mode             string        `json:"mode"`
	TerminalCode     string        `json:"terminal_code"`
	TerminalName     string        `json:"terminal_name"`
	Server           string        `json:"server"`
	Paired           bool          `json:"paired"`
	Online           bool          `json:"online"`
	LastPushAt       string        `json:"last_push_at"`
	Pending          int           `json:"pending"`
	Stuck            int           `json:"stuck"`
	PendingDocuments int           `json:"pending_documents"`
	Ranges           []NumberRange `json:"ranges"`
}

type NumberRange struct {
	Series    string `json:"series"`
	Remaining int    `json:"remaining"`
	Warning   bool   `json:"warning"`
}

// ReadStatus lee la última foto dejada por el demonio.
func ReadStatus(path string) (SyncStatus, error) {
	var status SyncStatus

	content, err := os.ReadFile(path)
	if err != nil {
		return status, err
	}

	if err := json.Unmarshal(content, &status); err != nil {
		return status, err
	}

	return status, nil
}

// Summary arma el texto del globo de la bandeja del sistema.
//
// Se prioriza lo que le importa a quien atiende: si está sincronizando o no,
// y cuánto quedó sin subir.
func (s SyncStatus) Summary() string {
	if !s.Paired {
		return "pro8 — sin parear con el servidor"
	}

	pending := s.Pending + s.PendingDocuments

	if !s.Online {
		if pending > 0 {
			return fmt.Sprintf("pro8 — SIN CONEXIÓN · %d sin subir", pending)
		}

		return "pro8 — SIN CONEXIÓN · todo al día"
	}

	if pending > 0 {
		return fmt.Sprintf("pro8 — en línea · subiendo %d", pending)
	}

	if s.Stuck > 0 {
		return fmt.Sprintf("pro8 — en línea · %d con error", s.Stuck)
	}

	return "pro8 — en línea · todo sincronizado"
}

// LowRanges devuelve las series cuya numeración está por agotarse. Es lo que
// dispara el aviso: quedarse sin números estando sin internet frena la venta.
func (s SyncStatus) LowRanges() []NumberRange {
	var low []NumberRange

	for _, r := range s.Ranges {
		if r.Warning {
			low = append(low, r)
		}
	}

	return low
}
