extends AliveObjectState

class_name CharacterStateDirectionRight

func process(subject)-> void:
	if not subject.character_direction == subject.CharacterDirectionEnum.RIGHT:
		subject.scale.x = -subject.scale.x
		subject.character_direction = subject.CharacterDirectionEnum.RIGHT
	
static func get_state_name() -> String:
	return "direction_right"
	
func get_type() -> int:
	return TypeEnum.CONTEXT
