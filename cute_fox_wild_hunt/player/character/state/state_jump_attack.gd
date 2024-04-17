extends AliveObjectState

class_name CharacterStateJumpAttack

const BUMP_AFTER_TIME = 0.3

func enter_triggered(subject) -> bool:
	return true

func leave_triggered(subject) -> bool:
	return true
	
func can_enter(subject) -> bool:
	return not state_machine.is_active("bounce")
	
func can_leave(subject) -> bool:
	return state_machine.is_active("bounce")

func enter(subject, context: Dictionary = {}) -> void:
	subject.character_jump_attack_area.connect("body_entered", self, "_on_jump_attack_area_body_entered")
	subject.character_jump_attack_area.connect("body_exited", self, "_on_jump_attack_area_body_exited")
	subject.character_jump_attack_collision_shape.disabled = false
	
	
	
func leave(subject) -> void:
	subject.character_jump_attack_area.disconnect("body_entered", self, "_on_jump_attack_area_body_entered")
	subject.character_jump_attack_area.disconnect("body_exited", self, "_on_jump_attack_area_body_exited")
	subject.character_jump_attack_collision_shape.disabled = true
	
func _on_jump_attack_area_body_entered(body):	
	if body.is_in_group("enemy"):
		var creature: Creature = body
		
		if body.is_in_group("bumpable"):
			creature.bump()
		
		state_machine.subject.character_jump_attack_sound.play()
		creature.apply_damage(get_jump_attack_damage())
		
func _on_jump_attack_area_body_exited(body):
	if body.is_in_group("enemy"):
		var creature: Creature = body
		
		if body.is_in_group("bumpable"):
			
			yield(state_machine.subject.get_tree().create_timer(BUMP_AFTER_TIME), "timeout")
			if not state_machine.subject.character_jump_attack_area.overlaps_body(body):
				creature.unbump()
		
func get_jump_attack_damage() -> int:
	return randi() % state_machine.subject.jump_attack_damage + 1

static func get_state_name() -> String:
	return "jump_attack"

func get_type() -> int:
	return TypeEnum.AURA
