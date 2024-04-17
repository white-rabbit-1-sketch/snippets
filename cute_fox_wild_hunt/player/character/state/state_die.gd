extends AliveObjectState

class_name CharacterStateDie

func enter_triggered(subject) -> bool:
	return true
	
func can_enter(subject) -> bool:
	return subject.health <= 0
	
func enter(subject, context: Dictionary = {}) -> void:
	state_machine.clear_but_types([TypeEnum.CONTEXT, TypeEnum.AURA])
	
	subject.set_process(false)
	subject.set_physics_process(false)
	subject.set_process_input(false)
	#character.character_collision_shape.disabled = true
	subject.character_sprite.priority_queue("die", 999)

static func get_state_name() -> String:
	return "die"
	
func get_type() -> int:
	return TypeEnum.CONTEXT
