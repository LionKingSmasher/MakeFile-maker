CC = g++
FLAGS = -std=c++1z -Wall
OUTPUT = start
OBJECT =  test.o
$(OUTPUT): $(OBJECT)
	$(CC) $(FLAGS) -o $@ $^

clean:
	rm *.o