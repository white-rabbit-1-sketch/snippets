extends AliveObjectState

class_name CharacterStateMoveLeft

func leave_triggered(subject) -> bool:
	return state_machine.is_active("bounce")

func can_enter(subject) -> bool:
	return (
		not state_machine.is_active("move_right")
		and not state_machine.is_active("bounce")
	)
	
func enter(subject, context: Dictionary = {}) -> void:
	state_machine.leave(state_machine.get_state("direction_right"))
	state_machine.enter(state_machine.get_state("direction_left"))
	
func process(subject)-> void:
	subject.movement.vector.x = -1
	subject.character_sprite.priority_queue("run", 1)

static func get_state_name() -> String:
	return "move_left"
