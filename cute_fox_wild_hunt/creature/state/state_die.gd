extends AliveObjectState

class_name CreatureStateDie
	
func enter_triggered(subject) -> bool:
	return true
	
func can_enter(subject) -> bool:
	return subject.health <= 0
	
func enter(subject, context: Dictionary = {}) -> void:
	state_machine.clear_but_types([TypeEnum.CONTEXT, TypeEnum.AURA])
	
	subject.set_process(false)
	subject.set_physics_process(false)
	subject.creature_collision_shape.remove_and_skip()
	subject.creature_die_sound.play()
	subject.creature_sprite.priority_queue('die', 10)
	yield(subject.creature_sprite, 'animation_finished')
	subject.queue_free()

static func get_state_name() -> String:
	return "die"
	
func get_type() -> int:
	return TypeEnum.CONTEXT
