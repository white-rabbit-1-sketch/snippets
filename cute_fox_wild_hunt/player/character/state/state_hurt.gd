extends AliveObjectState

class_name CharacterStateHurt

const HURT_TIME = 0.3
const MODULATE_COUNT = 3

var in_progress: bool = false

func leave_triggered(subject) -> bool:
	return true
	
func can_leave(subject) -> bool:
	return not in_progress
	
func enter(subject, context: Dictionary = {}) -> void:
	var damage: int = context.damage

	var health = subject.health - damage
	if health < 0: health = 0
	subject.health = health
	
	in_progress = true
	yield(subject.get_tree().create_timer(HURT_TIME), "timeout")
	in_progress = false
	
func process(subject)-> void:
	subject.character_sprite.priority_queue("hurt", 5)

static func get_state_name() -> String:
	return "hurt"
