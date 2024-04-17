extends AliveObjectState

class_name CreatureStateBounceAttack

enum BounceDirectionEnum {LEFT, RIGHT}

var bounce_direction: int

func enter_triggered(subject) -> bool:
	return true
	
func leave_triggered(subject) -> bool:
	return true

func can_enter(subject) -> bool:
	return not state_machine.is_active("bump")
	
func can_leave(subject) -> bool:
	return state_machine.is_active("bump")
	
func process(subject) -> void:
	var collisions = CollisionService.get_directional_collisions(subject)
	for collision in collisions:
		if not collision.collision.collider:
			continue
		
		if collision.collision.collider.is_in_group("player"):
			var character: Character = collision.collision.collider
			if character.state_machine.is_active("die"):
				continue
			
			bounce_direction = BounceDirectionEnum.LEFT
			if collision.h_side == Collision.DirectionEnum.RIGHT:
				bounce_direction = BounceDirectionEnum.RIGHT
			
			character.bounce(bounce_direction)
			character.apply_damage(subject.get_bounce_damage())
			
			break

static func get_state_name() -> String:
	return "bounce_attack"
