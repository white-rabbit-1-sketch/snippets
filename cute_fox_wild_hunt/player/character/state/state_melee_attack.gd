extends AliveObjectState

class_name CharacterStateMeleeAttack

const ATTACK_TIME = 0.3

var in_progress: bool

func leave_triggered(subject) -> bool:
	return true
	
func can_enter(subject) -> bool:
	return not state_machine.is_active("bounce")
	
func can_leave(subject) -> bool:
	return not in_progress

func enter(subject, context: Dictionary = {}) -> void:
	in_progress = true
	subject.character_melee_attack_area.connect("body_entered", self, "_on_melee_attack_area_body_entered")
	subject.character_melee_attack_collision_shape.disabled = false
	subject.character_melee_attack_sound.play()
	yield(subject.get_tree().create_timer(ATTACK_TIME), "timeout")
	subject.character_melee_attack_collision_shape.disabled = true
	subject.character_melee_attack_area.disconnect("body_entered", self, "_on_melee_attack_area_body_entered")
	in_progress = false
	
func _on_melee_attack_area_body_entered(body):
	if body.is_in_group("enemy"):
		var creature: Creature = body
		creature.apply_damage(get_melee_attack_damage())
		
func get_melee_attack_damage() -> int:
	return randi() % state_machine.subject.melee_attack_damage + 1
	
func process(subject) -> void:
	subject.character_sprite.priority_queue("attack", 6)

static func get_state_name() -> String:
	return "melee_attack"
