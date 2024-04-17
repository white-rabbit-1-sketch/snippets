package snapshot

import (
	"encoding/json"
	"log"
	"os"
)

type SnapshotDumper struct {
	snapshotFilePath string
}

func NewSnapshotDumper(snapshotFilePath string) *SnapshotDumper {
	return &SnapshotDumper{
		snapshotFilePath: snapshotFilePath,
	}
}

func (s *SnapshotDumper) SaveSnapshot(snapshot *Snapshot) {
	file, err := os.OpenFile(s.snapshotFilePath, os.O_CREATE|os.O_WRONLY, 0644)
	if err != nil {
		log.Fatal(err)
	}
	defer file.Close()

	jsonData, err := json.Marshal(snapshot)
	if err != nil {
		log.Fatal(err)
	}

	_, err = file.Write(jsonData)
	if err != nil {
		log.Fatal(err)
	}
}

func (s *SnapshotDumper) LoadSnapshot() *Snapshot {
	file, err := os.Open(s.snapshotFilePath)
	if err != nil {
		log.Fatal(err)
	}
	defer file.Close()

	snap := NewSnapshot()
	err = json.NewDecoder(file).Decode(&snap)
	if err != nil {
		log.Fatal(err)
	}

	return snap
}

func (s *SnapshotDumper) RemoveSnapshot() {
	err := os.Remove(s.snapshotFilePath)
	if err != nil {
		// don't do anything
	}
}
