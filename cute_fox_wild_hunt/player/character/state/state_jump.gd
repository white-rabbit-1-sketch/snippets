extends AliveObjectState

class_name CharacterStateJump

var jump_velocity: float = 0.0
	
func leave_triggered(subject) -> bool:
	return true

func can_enter(subject) -> bool:
	return subject.is_on_floor() and not state_machine.is_active("fall")

func can_leave(subject) -> bool:
	return jump_velocity <= 0.0
	
func enter(subject, context: Dictionary = {}) -> void:
	subject.character_jump_sound.play()
	jump_velocity = subject.jump_strength
	
func process(subject)-> void:
	subject.movement.vector.y = -jump_velocity
	jump_velocity -= (
		subject.movement.initial_vector.y * subject.get_physics_process_delta_time()  * 4
	)
	subject.character_sprite.priority_queue("jump", 3)

static func get_state_name() -> String:
	return "jump"
