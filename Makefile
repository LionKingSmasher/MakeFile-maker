CC = g++
FLAGS = -std=c++1z -Wall
OUTPUT = start
CCOBJECT = test.o
$(OUTPUT): $(CCOBJECT)
	$(CC) $(FLAGS) -o $@ $^

clean:
	rm *.o