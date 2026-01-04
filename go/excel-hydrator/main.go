package main

import (
	"encoding/json"
	"log"
	"net/http"
	"os"
	"path/filepath"
	"time"

	"github.com/xuri/excelize/v2"
)

type Request struct {
	Template string               `json:"template"`
	Data     map[string]SheetData `json:"data"`
}

type SheetData map[string]interface{}

func generateHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Only POST allowed", http.StatusMethodNotAllowed)
		return
	}

	var req Request
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid JSON", http.StatusBadRequest)
		return
	}

	templatePath := filepath.Join("/app/templates", req.Template)
	outputDir := "/app/output"

	if _, err := os.Stat(templatePath); os.IsNotExist(err) {
		http.Error(w, "Template not found", http.StatusNotFound)
		return
	}

	f, err := excelize.OpenFile(templatePath)
	if err != nil {
		log.Printf("OpenFile error: %v", err)
		http.Error(w, "Failed to open template", http.StatusInternalServerError)
		return
	}
	defer f.Close()

	// Обрабатываем каждый лист из req.Data
	for sheetName, sheetData := range req.Data {
		// Проверяем, существует ли лист
		sheetList := f.GetSheetList()
		found := false
		for _, name := range sheetList {
			if name == sheetName {
				found = true
				break
			}
		}
		if !found {
			http.Error(w, "Sheet "+sheetName+" not found", http.StatusNotFound)
			return
		}

		// Заполняем ячейки
		for cellRef, value := range sheetData {
			if err := f.SetCellValue(sheetName, cellRef, value); err != nil {
				log.Printf("SetCellValue %s!%s error: %v", sheetName, cellRef, err)
				http.Error(w, "Invalid cell address: "+cellRef, http.StatusBadRequest)
				return
			}
		}
	}

	// Генерируем имя файла и сохраняем
	filename := "hydrated_" + time.Now().Format("20060102_150405") + ".xlsx"
	outputPath := filepath.Join(outputDir, filename)

	// Принудительно включаем пересчёт формул при открытии в Excel
	calcMode := "auto"
	fullCalcOnLoad := true
	forceFullCalc := true

	err = f.SetCalcProps(&excelize.CalcPropsOptions{
		CalcMode:       &calcMode,
		FullCalcOnLoad: &fullCalcOnLoad,
		ForceFullCalc:  &forceFullCalc,
	})
	if err != nil {
		log.Printf("SetCalcProps error: %v", err)
		http.Error(w, "Failed to configure calculation", http.StatusInternalServerError)
		return
	}

	if err := f.SaveAs(outputPath); err != nil {
		log.Printf("SaveAs error: %v", err)
		http.Error(w, "Failed to save file", http.StatusInternalServerError)
		return
	}

	// Отдаем ответ
	resp := map[string]string{"filename": filename}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(resp)
}

func main() {
	http.HandleFunc("/generate", generateHandler)
	log.Println("Excel hydrator service listening on :8080")
	log.Fatal(http.ListenAndServe(":8080", nil))
}
