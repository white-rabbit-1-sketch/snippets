extends AliveObjectState

class_name CreatureStateMoveRight

func enter_triggered(subject) -> bool:
	return true
	
func leave_triggered(subject) -> bool:
	return true

func can_enter(subject) -> bool:
	return (
		not state_machine.is_active("die")
		and not state_machine.is_active("move_left")
		and not state_machine.is_active("bump")
		and not (
			subject.creature_horizontal_direction == subject.CreatureDirectionEnum.RIGHT
			and (
				subject.ray_cast_front.is_colliding()
				or not subject.ray_cast_down.is_colliding() 
			)
		)
	)
	
func can_leave(subject) -> bool:
	return (
		subject.ray_cast_front.is_colliding()
		or not subject.ray_cast_down.is_colliding() 
		or state_machine.is_active("die") 	
		or state_machine.is_active("bump") 
	)
	
func enter(subject, context: Dictionary = {}) -> void:
	state_machine.leave(state_machine.get_state("direction_left"))
	state_machine.enter(state_machine.get_state("direction_right"))

func process(subject) -> void:
	subject.movement.vector.x = 1
	subject.creature_sprite.priority_queue("run", 1)	

static func get_state_name() -> String:
	return "move_right"
