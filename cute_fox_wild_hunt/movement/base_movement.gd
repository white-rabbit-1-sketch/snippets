extends Node

class_name BaseMovement

var initial_vector
var initial_speed
	
var vector: Vector2
var speed: float

func _init(v: Vector2 = Vector2.ZERO, s: float = 1):
	initial_vector = v
	initial_speed = s
	
	reset()
	
func reset() -> void:
	vector = initial_vector
	speed = initial_speed
	
func clone() -> BaseMovement:
	var movement = self.duplicate()
	
	movement.initial_vector = initial_vector
	movement.initial_speed = initial_speed
	movement.reset()
	
	return movement

func merge(m: BaseMovement) -> void:
	initial_vector += m.initial_vector
	initial_speed += m.initial_speed
	
	reset()
