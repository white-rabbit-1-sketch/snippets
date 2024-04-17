extends AliveObjectState

class_name CreatureStateDirectionRight

func process(subject)-> void:
	if not subject.creature_horizontal_direction == subject.CreatureDirectionEnum.RIGHT:
		subject.scale.x = -subject.scale.x
		subject.creature_lifebar.rect_scale.x = -subject.creature_lifebar.rect_scale.x
		subject.creature_lifebar.rect_position.x = -subject.creature_lifebar.rect_position.x
		subject.creature_horizontal_direction = subject.CreatureDirectionEnum.RIGHT
	
static func get_state_name() -> String:
	return "direction_right"
	
func get_type() -> int:
	return TypeEnum.CONTEXT
