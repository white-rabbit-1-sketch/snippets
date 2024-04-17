extends AliveObjectState

class_name CharacterStateBounce

enum BounceDirectionEnum {LEFT, RIGHT}

const BOUNCE_STRENGTH: float = 2.0

var character_bounce_velocity: float = 0.0
var character_bounce_direction: int
	
func leave_triggered(subject) -> bool:
	return true

func can_enter(subject) -> bool:
	return not state_machine.is_active("die")
	
func can_leave(subject) -> bool:
	return character_bounce_velocity <= 0.0
	
func enter(subject, context: Dictionary = {}):
	character_bounce_direction = context.bounce_direction
	
	character_bounce_velocity = BOUNCE_STRENGTH
	subject.character_bounce_sound.play()
	
func process(subject)-> void:
	subject.movement.vector.y = -character_bounce_velocity
	if character_bounce_direction == BounceDirectionEnum.RIGHT:
		subject.movement.vector.x = character_bounce_velocity
	else: subject.movement.vector.x = -character_bounce_velocity
	
	character_bounce_velocity -= subject.movement.initial_vector.y * subject.get_physics_process_delta_time() * 4

static func get_state_name() -> String:
	return "bounce"
