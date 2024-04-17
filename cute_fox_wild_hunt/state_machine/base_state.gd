extends Node

class_name BaseState

var state_machine

func enter_triggered(subject) -> bool:
	return false
	
func leave_triggered(subject) -> bool:
	return false

func can_enter(subject) -> bool:
	return true
	
func can_leave(subject) -> bool:
	return true
	
func enter(subject, context: Dictionary = {}) -> void:
	pass
	
func leave(subject)-> void:
	pass

func process(subject)-> void:
	pass

static func get_state_name() -> String:
	assert(false, "Method get_state_name() must be implemented")
	
	return ""

func get_type() -> int:
	return 0
