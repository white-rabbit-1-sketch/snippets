extends AliveObjectState

class_name CharacterStateDirectionLeft

func process(subject)-> void:
	if not subject.character_direction == subject.CharacterDirectionEnum.LEFT:
		subject.scale.x = -subject.scale.x
		subject.character_direction = subject.CharacterDirectionEnum.LEFT
	
static func get_state_name() -> String:
	return "direction_left"
	
func get_type() -> int:
	return TypeEnum.CONTEXT
