package main

import (
	"crypto/rand"
	"fmt"
	"io"
	"strconv"
	"time"
)

func main() {

	id, err := aguid()

	if err != nil {
		fmt.Println("error: ", err)
	}

	fmt.Println(id)

	// What is the max?
	fmt.Println(int64(9223372036854775807))
}

func aguid() (int64, error) {

	c := 2
	b := make([]byte, c)
	n, err := io.ReadFull(rand.Reader, b)

	if n != len(b) || err != nil {
		return 0, err
	}

	// Convert to decimal
	randomness := int(b[0])<<8 | int(b[1])

	now := time.Now()
	milliseconds := now.Nanosecond() / 1e5
	seconds := int32(now.Unix())

	// Want to see the values?
	//fmt.Printf("%d-%d-%d\n", seconds, milliseconds, randomness)

	// We don't want to add, we want to concatenate
	s := fmt.Sprintf("%d%d%d", seconds, milliseconds, randomness)

	id, err := strconv.ParseInt(s, 10, 64)

	if err != nil {
		return 0, err
	}

	return id, err
}
