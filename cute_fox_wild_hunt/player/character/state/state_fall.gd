extends AliveObjectState

class_name CharacterStateFall

func enter_triggered(subject) -> bool:
	return true
	
func leave_triggered(subject) -> bool:
	return true

func can_enter(subject) -> bool:
	return (
		not subject.is_on_floor() 
		and not state_machine.is_active("jump")
	)
	
func can_leave(subject) -> bool:
	return subject.is_on_floor()

func process(subject)-> void:
	subject.character_sprite.priority_queue("fall", 2)

static func get_state_name() -> String:
	return "fall"
